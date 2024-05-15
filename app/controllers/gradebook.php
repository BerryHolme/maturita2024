<?php

namespace controllers;

class gradebook
{
    public function getGradebook(\Base $base)
    {
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
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            echo \Template::instance()->render("addstudent.html");
        } else {
            $base->reroute('/');
        }

    }

    public function postAddstudent(\Base $base)
    {
        $student = new \models\students();

        $student->copyfrom($base->get('POST'));

        $student->save();
        $base->reroute("board/");
    }

    public function studentsList(\Base $base)
    {
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
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $student = new \models\students();
            $studentDetail = $student->findone(['id = ?', $params['id']]);

            if ($studentDetail) {
                $records = new \models\record();
                $studentRecords = $records->find(['student = ?', $studentDetail->id]) ?: [];

                // Format the date for each record
                foreach ($studentRecords as $record) {
                    $record->date = date('d. m. Y', strtotime($record->date));
                }

                // Calculate the total excused hours
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
        $data = json_decode($base->get('BODY'), true);

        $record = new \models\record();
        $record->date = $data['date'];
        $record->hours = $data['hours'];
        $record->excused = $data['excused'];
        $record->student = $data['student'];
        $record->save();

        echo json_encode($record->cast());
    }

    public function deleteRecord(\Base $base, $params)
    {
        $record = new \models\record();
        $recordToDelete = $record->findone(['id = ?', $params['id']]);
        if ($recordToDelete) {
            $recordToDelete->erase();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function excuseRecord(\Base $base, $params)
    {
        $record = new \models\record();
        $recordToExcuse = $record->findone(['id = ?', $params['id']]);
        if ($recordToExcuse) {
            $recordToExcuse->excused = 1; // Set the record as excused
            $recordToExcuse->save();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function getTopExcusedStudents()
    {
        $students = new \models\students();
        $records = new \models\record();

        // Fetch all students and their total excused hours
        $allStudents = $students->find();
        if (!$allStudents) {
            // Log an error message and return an empty array
            error_log("No students found or database query failed.");
            return [];
        }

        $studentExcusedHours = [];

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

        // Sort students by total excused hours in descending order and get the top 5
        usort($studentExcusedHours, function ($a, $b) {
            return $b['excused_hours'] - $a['excused_hours'];
        });

        return array_slice($studentExcusedHours, 0, 5);
    }


}