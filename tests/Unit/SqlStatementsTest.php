<?php

use App\Support\SqlStatements;

test('scripts are split on semicolons outside quotes and comments', function () {
    $sql = "SELECT 1; -- note; ignored\nSELECT 'a;b' AS text; /* x; y */ UPDATE leads SET status = 'new';";

    expect(SqlStatements::split($sql))->toBe([
        'SELECT 1',
        "SELECT 'a;b' AS text",
        "UPDATE leads SET status = 'new'",
    ]);
});

test('read statements are told apart from writes', function (string $statement, bool $isRead) {
    expect(SqlStatements::isRead($statement))->toBe($isRead);
})->with([
    ['SELECT * FROM leads', true],
    ['  select 1', true],
    ['SHOW TABLES', true],
    ['EXPLAIN SELECT 1', true],
    ['(SELECT 1)', true],
    ['UPDATE leads SET status = 1', false],
    ['DELETE FROM leads', false],
    ['DROP TABLE leads', false],
    ['INSERT INTO leads VALUES (1)', false],
]);
