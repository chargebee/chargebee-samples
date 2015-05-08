package com.chargebee.samples.common;

import com.chargebee.*;
import java.io.*;
import java.io.File;
import java.util.*;
import java.util.logging.*;
import javax.servlet.*;

public class EnvInitializer implements ServletContextListener {

    ServletContext context;

    @Override
    public void contextInitialized(ServletContextEvent sce) {
        context = sce.getServletContext();
                
        /**
         * The credentials are stored in a properties file under WEB-INF
         * The live site api keys should be stored securely. It should preferably 
         * be stored only in the production machine(s) and not hard coded 
         * in code or checked into a version control system by mistake.
         */
        Properties credentials = read("WEB-INF/ChargeBeeCredentials.properties");
        Environment.configure(credentials.getProperty("site"), 
                credentials.getProperty("api_key"));
                
    }

    private Properties read(String credFilePath) {
        credFilePath = context.getRealPath(credFilePath);//Getting the full path
        System.out.println("\n\nConfiguring ChargeBee's environment from " + credFilePath + "\n\n");
        Reader credReader = null;
        try {
            credReader = new BufferedReader(new FileReader(new File(credFilePath)));
            Properties credProps = new Properties();
            credProps.load(credReader);
            return credProps;
        } catch (Exception ex) {
            throw new RuntimeException(ex);
        } finally {
            if (credReader != null) {
                try {
                    credReader.close();
                } catch (Exception ex) {
                    ex.printStackTrace();
                }
            }
        }
    }

    @Override
    public void contextDestroyed(ServletContextEvent sce) {
    }
}
