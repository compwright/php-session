Feature: Session Persistence
  Sessions store data that persists between page requests.
  Session data is stored in a designated place and format.

  Scenario Outline: Session data persists across requests
    Given session <handler> stored at <location>
    Then new session is started
    And session is writeable
    And session is saved and closed
    And further session writes are not saved
    Then previous session is started
    And session is readable
    And session can be reset
    Then previous session is started
    And session can be erased
    And session can be deleted

    Examples:
      | handler | location |
      | cache   | A        |
      | file    | B        |
