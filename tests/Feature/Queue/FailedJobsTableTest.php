<?php

namespace Tests\Feature\Queue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FailedJobsTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Production uses the Redis queue driver, so there is no committed `jobs`
        // migration. This test exercises queue:work via the database driver to
        // keep the suite Redis-free; the failed_jobs write path is identical
        // regardless of which driver dispatched the original job.
        if (! Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        config()->set('queue.default', 'database');
        config()->set('queue.connections.database', [
            'driver' => 'database',
            'connection' => null,
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ]);
    }

    public function test_failing_job_is_logged_to_failed_jobs_table(): void
    {
        Queue::push(new AlwaysThrowingTestJob);

        $this->assertDatabaseCount('jobs', 1);
        $this->assertDatabaseCount('failed_jobs', 0);

        $this->artisan('queue:work', [
            '--once' => true,
            '--tries' => 1,
        ])->assertExitCode(0);

        $this->assertDatabaseCount('failed_jobs', 1);

        $failure = DB::table('failed_jobs')->first();

        $this->assertSame('database', $failure->connection);
        $this->assertSame('default', $failure->queue);
        $this->assertStringContainsString('Job blew up on purpose', $failure->exception);
    }
}

class AlwaysThrowingTestJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        throw new \RuntimeException('Job blew up on purpose');
    }
}
