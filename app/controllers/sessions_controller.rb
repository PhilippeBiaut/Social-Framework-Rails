class SessionsController < ApplicationController
  allow_unauthenticated_access only: %i[ new create ]

  MAX_FAILED_ATTEMPTS = 10
  THROTTLE_WINDOW = 3.minutes

  def new
  end

  def create
    if throttled?
      redirect_to new_session_path, alert: "Too many login attempts. Please try again later."
      return
    end

    if user = User.authenticate_by(params.permit(:email_address, :password))
      clear_failed_attempts
      start_new_session_for user, remember: params[:remember].present?
      redirect_to after_authentication_url
    else
      record_failed_attempt
      redirect_to new_session_path, alert: "Try another email address or password."
    end
  end

  def destroy
    terminate_session
    redirect_to new_session_path, status: :see_other
  end

  private

  # Only *failed* attempts count, and a success clears the counter, so a busy
  # legitimate user is never locked out of their own account.
  def throttle_key
    "login-attempts:#{params[:email_address].to_s.strip.downcase}:#{request.remote_ip}"
  end

  def throttled?
    Rails.cache.read(throttle_key).to_i >= MAX_FAILED_ATTEMPTS
  end

  def record_failed_attempt
    count = Rails.cache.read(throttle_key).to_i + 1
    Rails.cache.write(throttle_key, count, expires_in: THROTTLE_WINDOW)
  end

  def clear_failed_attempts
    Rails.cache.delete(throttle_key)
  end
end
