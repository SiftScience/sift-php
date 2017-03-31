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
