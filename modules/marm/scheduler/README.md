marmalade :: Scheduler
======================
Tired of setting up cronjobs again and again?
We are!

The scheduler allows you to setup new task directly from your shop backend.
It takes care of the time, scheduling, deactivating in case of an error, etc. pp.

Author
------
We guys from marmalade GmbH, Jens Richter and Joscha Krug, startet that project.
Feel free to contribute and help us, make the module better and better.

Support
-------
You will get support in the OXID Forum (http:://forum.oxid-esales.com)
And for sure there is paid support from us.
Get in touch with us: mail at marmalade dot de

License
-------
This tool is released under the Terms of the MIT License.
So you could use it in OXID CE, PE and EE.

Building new tasks
------------------
That's easy.
Just make a new class with the public method "run".
The method should return an array with two entries:

1.    "success" with 1 / 0
2.    "message" with your message