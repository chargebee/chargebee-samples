/*
 * Copyright (c) 2011 chargebee.com
 * All Rights Reserved.
 */
package com.chargebee.samples.common;

import java.io.IOException;
import java.util.*;
import javax.servlet.RequestDispatcher;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.apache.commons.lang3.*;

public class ErrorServlet extends HttpServlet {
    
    @Override
    protected void service(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        Throwable th = (Throwable) req.getAttribute("javax.servlet.error.exception");
        Integer statusCode = (Integer) req.getAttribute("javax.servlet.error.status_code");
        if (statusCode == null) {
            statusCode = 404;//Assuming 404!!
        }
        if (th != null) {
            System.err.println("Error when processing " + req.getRequestURL());
            th.printStackTrace();
        }
        String errorPage;
        if (ArrayUtils.contains(new int[]{400, 403, 404}, statusCode)) {
            errorPage = statusCode + ".html";
        } else {
            errorPage = "500.html";
        }
        RequestDispatcher dispatcher = req.getRequestDispatcher("/error_pages/" + errorPage);
        dispatcher.forward(req, resp);
    }
}
