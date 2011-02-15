The best Amf remoting framework for Flash & Flex!
Some generated documentation can be found in doc/phpdoc
Proper documentation is on its way. The website for the project is here
http://projects.silexlabs.org/?/amfphp
The old website documentation, a lot of which is obsolete, is here
http://amfphp.sourceforge.net/docs/

notes on upgrading from 1.9 to 2.0
- if you use relative includes in your services, these might mess up. This is becuase of a chdir call in 1.9.
You can uncomment the chdir line in the gateway script to reproduce this behaviour.
- Anonymous objects are now deserialized as objects, not arrays.
Typed objects use a very simple convention: The type found when deserializing is set to « _explicitType » on the PHP Object.
And if you want to serialize an object with a type, all you have to do is make sure its « _explicitType » field is set.
The Custom Class Mapping helps you do things the old way, but if you want to do things differently, you can.
