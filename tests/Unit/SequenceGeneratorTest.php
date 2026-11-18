<?php

namespace Tests\Unit;

use App\Models\Branch;
use App\Services\SequenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SequenceGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequence_generator_increments_and_formats_properly(): void
    {
        $this->seed();

        $sequenceService = app(SequenceService::class);
        $pusat = Branch::where('code', 'PUSAT')->first();

        $num1 = $sequenceService->nextNumber($pusat, 'PJ');
        $num2 = $sequenceService->nextNumber($pusat, 'PJ');

        $today = now()->format('Ymd');
        $this->assertEquals("PUSAT-PJ-{$today}-0001", $num1);
        $this->assertEquals("PUSAT-PJ-{$today}-0002", $num2);
    }
}
