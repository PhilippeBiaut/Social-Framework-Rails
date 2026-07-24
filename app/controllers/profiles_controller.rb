class ProfilesController < ApplicationController
  def edit
    @user = current_user
  end

  def update
    @user = current_user

    if @user.update(profile_params)
      redirect_to user_path(@user), notice: "Profile updated."
    else
      render :edit, status: :unprocessable_entity
    end
  end

  private

  def profile_params
    params.expect(user: %i[username name bio avatar])
  end
end
