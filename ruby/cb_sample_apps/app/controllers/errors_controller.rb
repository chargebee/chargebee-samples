class ErrorsController < ApplicationController

  def error_400
     render file: "#{Rails.root}/public/error_pages/400.html", status: 400, layout: false
  end
  
  def error_404
    render file: "#{Rails.root}/public/error_pages/404.html", status: 404, layout: false
  end
  
  def error_500
    render file: "#{Rails.root}/public/error_pages/500.html", status: 500, layout: false
  end
  
end
