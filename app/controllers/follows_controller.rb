class FollowsController < ApplicationController
  before_action :set_user

  def create
    current_user.follow(@user)
    respond_with_button
  end

  def destroy
    current_user.unfollow(@user)
    respond_with_button
  end

  private

  def set_user
    @user = User.find_by!(username: params[:user_username])
  end

  def respond_with_button
    @user.reload
    respond_to do |format|
      format.turbo_stream do
        render turbo_stream: [
          turbo_stream.replace("user_#{@user.id}_follow", partial: "follows/button", locals: { user: @user }),
          turbo_stream.replace("user_#{@user.id}_followers_count", partial: "users/followers_count", locals: { user: @user })
        ]
      end
      format.html { redirect_back fallback_location: user_path(@user) }
    end
  end
end
