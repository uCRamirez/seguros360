<?php

namespace App\Imports;

use App\Models\NotesTypification;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;

class NotesTypificationImport implements ToArray, WithHeadingRow
{
    public function array(array $notesTypifications)
    {

        DB::transaction(function () use ($notesTypifications) {
            foreach ($notesTypifications as $typification) {

                if (
                    !array_key_exists('typification_1', $typification) || !array_key_exists('typification_2', $typification) || !array_key_exists('typification_3', $typification) || !array_key_exists('typification_4', $typification)
                ) {
                    throw new ApiException('Field missing from header.');
                }

                if ($typification['typification_1']) {
                    $typification1 = new NotesTypification();
                    $typification1->name = $typification['typification_1'];

                    if(!$typification['typification_2']){
                        $typification1->sale = $typification['sale'] ?? false;
                        $typification1->schedule = $typification['schedule'] ?? false;
                    }

                    $typification1->name ? $typification1->save() : '';
                }

                if ($typification['typification_1'] || $typification['typification_2']) {
                    $typification2 = new NotesTypification();
                    $typification2->name = $typification['typification_2'];
                    $typification2->parent_id = $typification1->id;

                    if(!$typification['typification_3']){
                        $typification2->sale = $typification['sale'] ?? false;
                        $typification2->schedule = $typification['schedule'] ?? false;
                    }
                    
                    $typification2->name ? $typification2->save() : '';
                }

                if ($typification['typification_1'] && $typification['typification_2'] && $typification['typification_3']) {
                    $typification3 = new NotesTypification();
                    $typification3->name = $typification['typification_3'];
                    $typification3->parent_id = $typification2->id;

                    if(!$typification['typification_4']){
                        $typification3->sale = $typification['sale'] ?? false;
                        $typification3->schedule = $typification['schedule'] ?? false;
                    }

                    $typification3->name ? $typification3->save() : '';
                }

                if ($typification['typification_1'] && $typification['typification_2'] && $typification['typification_3'] && $typification['typification_4']) {
                    $typification4 = new NotesTypification();
                    $typification4->name = $typification['typification_4'];
                    $typification4->parent_id = $typification3->id;
                    $typification4->sale = $typification['sale'] ?? false;
                    $typification4->schedule = $typification['schedule'] ?? false;
                    $typification4->name ? $typification4->save() : '';
                }

            }
        });
    }
}