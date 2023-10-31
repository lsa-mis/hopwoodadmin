<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/configEnglishContestAdmin.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../Support/basicLib.php');

if ($isAdmin) {
    // output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    // header('Content-Disposition: attachment; filename="Hopwood-Kitchensink.csv"');
    header('Content-Disposition: attachment; filename="ALL_Hopwood_Entries_Since_2015.csv"');

    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // output the column headings
    fputcsv($output, array('uniqname', 'UMid', 'Name', 'Local_Address', 'Home_Address', 'ClassLevel', 'Campus_Location','Grad_Yr-Mo/School/Dept/Major', 'Financial-Aid', 'Financial-Aid-Desciption', 'Title-of-Manuscript', 'Type-of-Manuscript', 'Name-of-Contest', 'Qualifying-Course', 'Qualifying-Instructor', 'Qualifying-Term_Year', 'Hometown-Newspaper', 'Publication-Name', 'Pen_Name', 'status'));

                $sqlSelect2 = <<<SQL
                    SELECT *
                    FROM  vw_kitchensink2
SQL;

    if (!$result = $db->query($sqlSelect2)) {
        db_fatal_error("data select issue", $db->error);
        exit;
    }

    // loop over the rows, outputting them
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fputcsv($output, array("STATUS KEY: Active: 0, Deleted: 1, Archived: 2, Disqualified: 3"));
} else {
    echo "You are not allowed to view this stuff!";
}