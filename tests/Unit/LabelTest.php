<?php

namespace Tests\Unit;

use App\Models\Label;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_label_can_be_created()
    {
        $label = Label::factory()->create([
            'name' => 'Example Label',
            'description' => 'Example Description',
        ]);

        $this->assertDatabaseHas('labels', [
            'id' => $label->id,
            'name' => 'Example Label',
            'description' => 'Example Description',
        ]);
    }

    public function test_label_can_be_updated()
    {
        $label = Label::factory()->create();

        $label->update([
            'name' => 'Updated Label',
            'description' => 'Updated Description',
        ]);

        $this->assertDatabaseHas('labels', [
            'id' => $label->id,
            'name' => 'Updated Label',
            'description' => 'Updated Description',
        ]);
    }
}
