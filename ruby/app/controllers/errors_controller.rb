class ErrorsController < ApplicationController
  
  def error_404
    render file: "#{Rails.root}/public/error_pages/404.html", status: 404
  end
  
  def error_500
    render file: "#{Rails.root}/public/error_pages/500.html", status: 500
  end
  
end