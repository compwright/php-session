Feature: Session ID
  Each session is referenced by a unique ID.
  If no ID is provided, one will be generated.
  If an invalid ID is provided, a new one will be generated.
  Generated IDs may be prefixed with user information.
  Generated IDs are guaranteed to not collide with other session IDs.

  Scenario: Default Session ID settings
    When default configuration
    Then length is 32 and bits is 4

  Scenario Outline: Generate a Session ID
    Given default configuration
    And <bits>, <length>, and <prefix>
    When Generating an ID
    Then length must be <length>
    And the ID must be allowed characters
    And it must start with <prefix>
    Examples:
      | bits | length | prefix |
      | 4    | 24     | Jo     |
      | 5    | 32     | Sally  |
      | 6    | 256    | Marge  |

  Scenario: No Session ID is provided
    Given no ID
    When session is started
    Then ID should be generated

  Scenario: Invalid Session ID is provided
    Given invalid ID
    When session is started
    Then ID should be generated

  Scenario: No Session ID collisions
    Given 4 bits and 22 characters
    And 4000000 IDs already exist
    When 100000 IDs are generated
    Then there are 4100000 IDs and no collisions
