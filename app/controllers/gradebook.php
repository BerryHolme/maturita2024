<?php

namespace controllers;

class gradebook
{
    public function getGradebook(\Base $base)
    {
        //otevre tridnici
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $topStudents = $this->getTopExcusedStudents();
            $base->set('topStudents', $topStudents);
            echo \Template::instance()->render("gradebook.html");
        } else {
            $base->reroute('/');
        }
    }

    public function addstudent(\Base $base)
    {
        //otevre form pro pridani studenta
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            echo \Template::instance()->render("addstudent.html");
        } else {
            $base->reroute('/');
        }

    }

    public function postAddstudent(\Base $base)
    {
        //prida studenta
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $student = new \models\students();
            if (!$base->get('POST.name')) {
                echo "Jméno je prázdné";
                return;
            }
            if (!$base->get('POST.surname')) {
                echo "Příjmení je prázdné";
                return;
            }
            if ($base->get('POST.phone')) {
                if(!is_numeric($base->get('POST.phone'))){
                    echo "Telefon není číslo";
                    return;
                }

            }else{
                echo "Telefon je prázný";
                return;
            }

            $commute = $base->get('POST.commute');
            if (isset($commute) && ($commute != '0' && $commute != 'on')) {
                echo "Commute není bool  ";
                echo $commute;
                return;
            }

            $student->copyfrom($base->get('POST'));

            $student->save();
            $base->reroute("board/");
        } else {
            $base->reroute('/');
        }
    }

    public function studentsList(\Base $base)
    {
        //zobrazi seznam studentu
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $students = new \models\students();
            $base->set('students', $students->find());
            echo \Template::instance()->render("studentsList.html");
        } else {
            $base->reroute('/');
        }
    }

    public function studentDetail(\Base $base, $params)
    {
        //zobrazi detail studenta
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $student = new \models\students();
            $studentDetail = $student->findone(['id = ?', $params['id']]);

            if ($studentDetail) {
                $records = new \models\record();
                $studentRecords = $records->find(['student = ?', $studentDetail->id]) ?: [];

                // formatuje cas
                foreach ($studentRecords as $record) {
                    $record->date = date('d. m. Y', strtotime($record->date));
                }

                // celkovy hodiny
                $totalExcusedHours = 0;
                $totalNotexcusedHours = 0;
                $totalAbsence = 0;
                foreach ($studentRecords as $record) {
                    if ($record->excused) {
                        $totalExcusedHours += $record->hours;
                    }else{
                        $totalNotexcusedHours +=$record->hours;
                    }
                    $totalAbsence +=$record->hours;
                }

                $base->set('student', $studentDetail);
                $base->set('records', $studentRecords);
                $base->set('totalExcusedHours', $totalExcusedHours);
                $base->set('totalNotexcusedHours', $totalNotexcusedHours);
                $base->set('totalAbsence', $totalAbsence);
                echo \Template::instance()->render("studentDetails.html");
            } else {
                $base->reroute('students/');
            }
        } else {
            $base->reroute('/');
        }
    }



    public function postAddRecord(\Base $base)
    {
        //EEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEo
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $data = json_decode($base->get('BODY'), true);

            $record = new \models\record();
            $record->date = $data['date'];
            $record->hours = $data['hours'];
            $record->excused = $data['excused'];
            $record->student = $data['student'];
            $record->save();

            echo json_encode($record->cast());
        } else {
            $base->reroute('/');
        }


    }

    public function deleteRecord(\Base $base, $params)
    {
        //tohle rado maze veci tak snad to nekdo neprosvisti botem xd
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $record = new \models\record();
            $recordToDelete = $record->findone(['id = ?', $params['id']]);
            if ($recordToDelete) {
                $recordToDelete->erase();
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            $base->reroute('/');
        }

    }

    public function excuseRecord(\Base $base, $params)
    {
        // oznaci omluvenku za omluvenou
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $record = new \models\record();
            $recordToExcuse = $record->findone(['id = ?', $params['id']]);
            if ($recordToExcuse) {
                $recordToExcuse->excused = 1;
                $recordToExcuse->save();
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            $base->reroute('/');
        }

    }

    public function getTopExcusedStudents()
    {
        // top 5 studentu s nejlepsim score
            $students = new \models\students();
            $records = new \models\record();


            $allStudents = $students->find();
            if (!$allStudents) {

                return [];
            }

            $studentExcusedHours = [];

            //projede a nastavi
            foreach ($allStudents as $student) {
                $totalExcusedHours = 0;
                $studentRecords = $records->find(['student = ?', $student->id]);
                if ($studentRecords) {
                    foreach ($studentRecords as $record) {

                        $totalExcusedHours += $record->hours;

                    }
                }
                $studentExcusedHours[] = [
                    'student' => $student,
                    'excused_hours' => $totalExcusedHours
                ];
            }

            // sorting
            usort($studentExcusedHours, function ($a, $b) {
                return $b['excused_hours'] - $a['excused_hours'];
            });

            return array_slice($studentExcusedHours, 0, 5);
    }


}