--TEST--
Simple Query Test
--DESCRIPTION--
Basic verification of query statements (via "sqlsrv_query"):
- Establish a connection
- Creates a table (including all 28 SQL types currently supported)
- Executes a SELECT query (on the empty table)
- Verifies the outcome
--ENV--
PHPT_EXEC=true
--SKIPIF--
<?php require('skipif_versions_old.inc'); ?>
--FILE--
<?php

require_once('MsCommon.inc');

function simpleQuery()
{
    $testName = "Statement - Simple Query";
    startTest($testName);

    setup();
    $tableName = 'TC31test';

    $conn1 = AE\connect();

    // just create an empty table
    $stmt1 = sqlsrv_query($conn1, "CREATE TABLE $tableName (dummyColumn int)");

    trace("Executing SELECT query on $tableName ...");
    $stmt1 = sqlsrv_query($conn1, "SELECT * FROM $tableName");
    $rows = rowCount($stmt1);
    sqlsrv_free_stmt($stmt1);
    trace(" $rows rows retrieved.\n");

    if ($rows > 0) {
        die("Table $tableName, expected to be empty, has $rows rows.");
    }

    dropTable($conn1, $tableName);

    sqlsrv_close($conn1);

    endTest($testName);
}

try {
    simpleQuery();
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
--EXPECT--
Test "Statement - Simple Query" completed successfully.
