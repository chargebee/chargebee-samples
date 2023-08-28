# Java Samples

To try out the samples please follow the below steps

* Install tomcat. You can get it from [Tomcat site].
* Download the ChargeBee's [java library].
* Copy the library jar to java/webapp/WEB-INF/lib  in this repo.
* Compile the classes using ant -f build/build.xml -Dtomcat.home=&lt;tomcat home&gt;
* **Note:** The samples need to run in the root context ("/"). So in &lt;tomcat home&gt;/conf/server.xml add a context path with docbase set to chargebee-samples/java/webapp and path set as "/". Or you could also copy the files to the ROOT context.
* Start the tomcat server.

[enable]: https://www.digitalocean.com/community/tutorials/how-to-set-up-mod_rewrite
[Chargebee tutorials]: https://chargebee.com/tutorials
[Tomcat site]: http://tomcat.apache.org/download-70.cgi "Tomcat site"
[java library]: https://github.com/chargebee/chargebee-java/tree/master/dist
[php library]: https://github.com/chargebee/chargebee-php/tags
