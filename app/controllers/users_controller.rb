class UsersController < ApplicationController
  before_action :set_user, only: %i[show following followers]

  def index
    @users = User.where.not(id: current_user.id).order(created_at: :desc)
    @users = @users.where("username LIKE :q OR name LIKE :q", q: "%#{params[:q]}%") if params[:q].present?
  end

  def show
    @posts = @user.posts.includes(:user, :likes, image_attachment: :blob).recent
    @liked = @user.liked_posts.includes(:user, :likes, image_attachment: :blob).order("posts.created_at DESC")
  end

  def following
    @users = @user.following.order(:username)
    render :connections, locals: { title: "#{@user.display_name} follows" }
  end

  def followers
    @users = @user.followers.order(:username)
    render :connections, locals: { title: "Followers of #{@user.display_name}" }
  end

  private

  def set_user
    @user = User.find_by!(username: params[:username])
  end
end
