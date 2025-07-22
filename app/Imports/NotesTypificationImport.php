<?php

namespace App\Imports;

use App\Models\NotesTypification;
use App\Models\Campaign;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Validation\ValidationException;
class NotesTypificationImport implements ToArray, WithHeadingRow
{
    public function array(array $notesTypifications)
    {

        DB::transaction(function () use ($notesTypifications) {
            foreach ($notesTypifications as $index => $typification) {
                if (
                    !array_key_exists('typification_1', $typification) || 
                    !array_key_exists('typification_2', $typification) || 
                    !array_key_exists('typification_3', $typification) || 
                    !array_key_exists('typification_4', $typification)
                ) {
                    throw new ApiException('Field missing from header.');
                }

                if (empty($typification['campaign'])) {
                    throw new ApiException('Campaign name is required. Error at Excel line: ' . ($index + 2));
                }

                $campaignId = Campaign::where('name', $typification['campaign'])->value('id');
                if (!$campaignId) {
                    throw new ApiException('Campaign not found: "' . $typification['campaign'] . '" at Excel line: ' . ($index + 2));
                }

                $parentId = null;
                $existing = true;
                $levels = ['typification_1', 'typification_2', 'typification_3', 'typification_4'];
                $lastNodeId = null;

                foreach ($levels as $levelIndex => $levelKey) {
                    $name = $typification[$levelKey] ?? null;
                    if (!$name) break;

                    $query = NotesTypification::where('name', $name)
                        ->where('parent_id', $parentId);

                    // Solo en el primer nivel se compara campaign_id
                    if ($parentId === null) {
                        $query->where('campaign_id', $campaignId);
                    }

                    $existingNode = $query->first();

                    if ($existingNode) {
                        $parentId = $existingNode->id;
                        $lastNodeId = $existingNode->id;
                        continue;
                    } else {
                        $existing = false;

                        $newNode = new NotesTypification();
                        $newNode->name = $name;
                        $newNode->parent_id = $parentId;

                        // Solo asignar campaign_id si es el nivel 1
                        if ($parentId === null) {
                            $newNode->campaign_id = $campaignId;
                        }

                        // Solo asignar sale/schedule si es el último nodo o el siguiente está vacío
                        $isLast = ($levelIndex == count($levels) - 1) || empty($typification[$levels[$levelIndex + 1]]);
                        if ($isLast) {
                            $newNode->sale = $typification['sale'] ?? false;
                            $newNode->schedule = $typification['schedule'] ?? false;
                        }

                        // Solo asignar no_contact si estamos en nivel 3
                        if ($levelIndex == 2) {
                            $newNode->no_contact = $typification['no_contact'] ?? false;
                        }


                        $newNode->save();
                        $parentId = $newNode->id;
                        $lastNodeId = $newNode->id;
                    }
                }

                // Si toda la ruta ya existía, no hacer nada
                if ($existing) {
                    continue;
                }
            }

        });
    }
}

// foreach ($notesTypifications as $index => $typification) {

            //     if (
            //         !array_key_exists('typification_1', $typification) || !array_key_exists('typification_2', $typification) || !array_key_exists('typification_3', $typification) || !array_key_exists('typification_4', $typification)
            //     ) {
            //         throw new ApiException('Field missing from header.');
            //     }

            //     if($typification['campaign'] === null || $typification['campaign'] === '') {
            //         throw new ApiException('Campaign name is required. Error at Excel line: ' . ($index + 2));
            //     }

            //     $campaignId = Campaign::where('name', $typification['campaign'])
            //         ->value('id');
            //     if($campaignId === null || $campaignId === 0 || $campaignId === '') {
            //         throw new ApiException('Campaign not found: "' . $typification['campaign'] . '" . Error at Excel line: ' . ($index + 2));
            //     }

            //     if($typification['typification_1'] === null || $typification['typification_1'] === '') {
            //         throw new ApiException('The typification_1 is obligatory. Error at Excel line: ' . ($index + 2));
            //     }

            //     if ($typification['typification_1']) {
            //         $typification1 = new NotesTypification();
            //         $typification1->campaign_id = $campaignId;
            //         $typification1->name = $typification['typification_1'];

            //         if(!$typification['typification_2']){
            //             $typification1->sale = $typification['sale'] ?? false;
            //             $typification1->schedule = $typification['schedule'] ?? false;
            //         }

            //         $typification1->name ? $typification1->save() : '';
            //     }

            //     if ($typification['typification_1'] || $typification['typification_2']) {
            //         $typification2 = new NotesTypification();
            //         $typification2->name = $typification['typification_2'];
            //         $typification2->parent_id = $typification1->id;

            //         if(!$typification['typification_3']){
            //             $typification2->sale = $typification['sale'] ?? false;
            //             $typification2->schedule = $typification['schedule'] ?? false;
            //         }
                    
            //         $typification2->name ? $typification2->save() : '';
            //     }

            //     if ($typification['typification_1'] && $typification['typification_2'] && $typification['typification_3']) {
            //         $typification3 = new NotesTypification();
            //         $typification3->name = $typification['typification_3'];
            //         $typification3->parent_id = $typification2->id;

            //         if(!$typification['typification_4']){
            //             $typification3->sale = $typification['sale'] ?? false;
            //             $typification3->schedule = $typification['schedule'] ?? false;
            //         }

            //         $typification3->name ? $typification3->save() : '';
            //     }

            //     if ($typification['typification_1'] && $typification['typification_2'] && $typification['typification_3'] && $typification['typification_4']) {
            //         $typification4 = new NotesTypification();
            //         $typification4->name = $typification['typification_4'];
            //         $typification4->parent_id = $typification3->id;
            //         $typification4->sale = $typification['sale'] ?? false;
            //         $typification4->schedule = $typification['schedule'] ?? false;
            //         $typification4->name ? $typification4->save() : '';
            //     }

            // }