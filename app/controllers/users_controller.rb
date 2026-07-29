class UsersController < ApplicationController
  before_action :set_user, only: %i[show following followers]

  def index
    # `id` breaks ties: users seeded in the same second would otherwise come
    # back in whatever order the database felt like.
    @users = User.where.not(id: current_user.id).order(created_at: :desc, id: :desc)

    if params[:q].present?
      # Escape %/_ so they are searched literally instead of acting as wildcards.
      term = "%#{User.sanitize_sql_like(params[:q])}%"
      @users = @users.where("username LIKE :q OR name LIKE :q", q: term)
    end
  end

  def show
    @posts = @user.posts.with_card_data.recent
    @liked = @user.liked_posts.with_card_data.recent
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
