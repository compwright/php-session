Feature: Garbage Collection
  Obsolete session data should be removed as soon as possible, but not instantly.
  Garbage collection can be run on a scheduler by a task scheduler.
  Garbage collection can run from time to time based on probability.

  Scenario: Garbage should remain until collected
    Given there is garbage to collect
      | id | last_modified |
      | 1  | -3 week       |
      | 2  | -2 week       |
      | 3  | -1 week       |
      | 4  | -3 day        |
      | 5  | -2 day        |
      | 6  | -1 day        |
      | 7  | -3 hour       |
      | 8  | -2 hour       |
      | 9  | -1 hour       |
      | 10 | -3 minute     |
    Given garbage collection is disabled
    When session is started
    Then garbage should remain

  Scenario: Run garbage collection on a schedule
    Given there is garbage to collect
      | id | last_modified |
      | 1  | -3 week       |
      | 2  | -2 week       |
      | 3  | -1 week       |
      | 4  | -3 day        |
      | 5  | -2 day        |
      | 6  | -1 day        |
      | 7  | -3 hour       |
      | 8  | -2 hour       |
      | 9  | -1 hour       |
      | 10 | -3 minute     |
    When garbage collection is run
    Then garbage should be collected

  Scenario Outline: Run garbage collection from time to time
    Given there is garbage to collect
      | id | last_modified |
      | 1  | -3 week       |
      | 2  | -2 week       |
      | 3  | -1 week       |
      | 4  | -3 day        |
      | 5  | -2 day        |
      | 6  | -1 day        |
      | 7  | -3 hour       |
      | 8  | -2 hour       |
      | 9  | -1 hour       |
      | 10 | -3 minute     |
    And probability is set to <probability> / <divisor>
    When session is started <probability> times
    Then prior garbage should be collected

    Examples:
      | probability | divisor |
      | 1           | 1       |
      | 1           | 2       |
      | 1           | 10      |
