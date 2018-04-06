3.0.1 (2018-04-06)
=================

- Updated documentation in CHANGES.RST and README.md


3.0.0 (2018-03-05)
=================

- Adds support for Sift Science API Version 205, including new [`$create_content`](https://siftscience.com/developers/docs/curl/events-api/reserved-events/create-content) and [`$update_content`](https://siftscience.com/developers/docs/curl/events-api/reserved-events/update-content) formats
- V205 APIs are now called -- **this is an incompatible change**
   - Use `:version => 204` when constructing the Client to call the previous API version
- Adds support for content decisions to [Decisions API](https://siftscience.com/developers/docs/curl/decisions-api)


INCOMPATIBLE CHANGES INTRODUCED IN API V205:
- `$create_content` and `$update_content` have significantly changed, and the old format will be rejected
- `$send_message` and `$submit_review` events are no longer valid
- V205 improves server-side event data validation.  In V204 and earlier, server-side validation accepts some events that did not conform to the published APIs in our [developer documentation](https://siftscience.com/developers/docs/curl/events-api).  V205 does not modify existing event APIs, but may reject invalid event data that were previously accepted.  **Please test your integration on V205 in sandbox before using in production.**

2.2.0 (2018-02-12)
=================
* Add session level decisions in Apply Decisions APIs.

2.1.1 (2017-09-13)
=================
* Adds logging capabilities
* Fixes warning logs on empty response body
* Fixes phpdoc

2.1.0 (2017-03-30)
=================
* Adds ability to use new decisions endpoints.

2.0.1 (2016-09-29)
=================
* fixes unlabel function to use array params instead of private variables.

2.0.0 (2016-07-19)
=================
* adds support for v204 of Sift Science's APIs
* adds Workflow Status API, User Decisions API, Order Decisions API
* v204 APIs are now called by default -- this is an incompatible change
  (use :version => 203 to call the previous API version)
* uses Hash arg for optional params in Client methods -- incompatible change

1.2.2 (2016-04-14)
=================
* Add return_action support to track method

1.2.1 (2016-02-29)
==================
* Add TravisCI
* Code cleanups

1.2.0 (2015-02-04)
==================
* Added Unlabel functionality
* Fixed minor bugs

1.1.1.2 (2015-01-12)
===================
* Fixed minor bugs with constant variables
