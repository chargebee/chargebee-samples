package com.chargebee.samples.common;

import com.chargebee.*;
import com.chargebee.internal.*;
import com.chargebee.org.json.*;
import java.io.*;
import java.net.*;
import java.util.logging.*;
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
}
