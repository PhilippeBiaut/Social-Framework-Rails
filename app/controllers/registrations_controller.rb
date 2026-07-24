class RegistrationsController < ApplicationController
  allow_unauthenticated_access only: %i[new create]

  def new
    @user = User.new
  end

  def create
    @user = User.new(registration_params)

    if @user.save
      start_new_session_for @user
      redirect_to after_authentication_url, notice: "Welcome aboard, #{@user.display_name} 👋"
    else
      render :new, status: :unprocessable_entity
    end
  end

  private

  def registration_params
    params.expect(user: %i[email_address username name bio password password_confirmation avatar])
  end
end
