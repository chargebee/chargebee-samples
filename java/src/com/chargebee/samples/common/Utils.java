package com.chargebee.samples.common;

import com.chargebee.*;
import com.chargebee.internal.*;
import com.chargebee.models.Subscription;
import com.chargebee.models.Subscription.Status;
import com.chargebee.org.json.*;
import java.io.*;
import java.net.*;
import java.sql.Timestamp;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.logging.*;
import javax.servlet.http.HttpServletRequest;
import org.apache.commons.io.*;
import org.apache.commons.lang3.*;

public class Utils {

    /**
     * Escapes html special characters. If the value is null then returns empty
     * string. This is imported in header.jspf as a static method to be used
     * easily inside the jsps.
     */
    public static String esc(String value) {
        return value == null ? "" : StringEscapeUtils.escapeHtml4(value);
    }

    public static String encodeParam(String value) throws UnsupportedEncodingException {
        return URLEncoder.encode(value, "UTF-8");
    }

    public static void log(String fileName, String key, ResultBase rs) {
        _log(fileName,key,rs);
    }

    public static void log(String fileName, String key, APIException ex) {
        _log(fileName,key,ex);
    }
    
    private static void _log(String fileName, String key, Object rs) {        
        String parDir = System.getenv("json_log_dir");
        if (parDir == null) {
            parDir = ".";
        }
        try {
            File jsonFile = new File(parDir + "/" + fileName);
            JSONObject obj;
            if (jsonFile.isFile()) {
               String content = FileUtils.readFileToString(jsonFile, "UTF-8");
               obj = new JSONObject(content);
            } else {
               obj = new JSONObject();
            }
            obj.put(key, new JSONObject(rs.toString()));
            FileUtils.writeStringToFile(jsonFile, obj.toString(3));
        } catch (Exception ex) {
            ex.printStackTrace();
        }
    }
    
    public static void validateParameters(HttpServletRequest req) {
        
        /* Your own custom implementation for validating form input parameters.
	 *
	 * Please visit ChargeBee apidocs(https://apidocs.chargebee.com/docs/api?lang=java) 
	 * for each input parameters validation constraint.
	 *
	 * Please validate as per the rules specified in apidocs for each parameter 
	 * and then call ChargeBee API to avoid parameter errors from ChargeBee.
         */
    }
    
    public static String getHostUrl(HttpServletRequest request) {
        return request.getScheme() + "://" + request.getServerName()
                + ":" + request.getServerPort();
        
    }
    public static String getHumanReadableDate(Timestamp timestamp) {
        SimpleDateFormat dateFormat = new SimpleDateFormat("dd-MMM-yy");
        String formatedDate = dateFormat.format( new Date(timestamp.getTime()));
        return Utils.esc(formatedDate);
    }
    
    public static Map subscriptionStatus() {
        Map cssClass = new HashMap<String, String>();
        cssClass.put(Subscription.Status.ACTIVE , "label-success");
        cssClass.put(Subscription.Status.IN_TRIAL, "label-default");
        cssClass.put(Subscription.Status.NON_RENEWING, "label-warning");
        cssClass.put(Subscription.Status.CANCELLED, "label-danger");
        cssClass.put(Subscription.Status.FUTURE, "label-primary");
        return cssClass;
    }
    
}
