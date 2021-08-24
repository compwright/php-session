Feature: Concurrency
  Sessions are protected from write contention to avoid corruption.
  Sessions changes may be committed early.

  Scenario: Normal write
    Given session has started
    When session changes
    Then commit should succeed

  Scenario: Write conflict
    Given session has started
    And session has been changed
    When session changes
    Then commit should fail
