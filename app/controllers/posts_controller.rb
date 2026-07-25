class PostsController < ApplicationController
  POSTS_PER_PAGE = 10

  before_action :set_post, only: %i[show]

  def index
    @tab = params[:tab] == "explore" ? "explore" : "following"

    scope = @tab == "explore" ? Post.all : current_user.feed

    # Ordering by id (not created_at) keeps the feed consistent with the `before`
    # cursor below: ids are monotonic, so pages can't overlap or skip rows.
    scope = scope.with_card_data.order(id: :desc)
    scope = scope.where("posts.id < ?", params[:before]) if params[:before].present?

    records   = scope.limit(POSTS_PER_PAGE + 1).to_a
    @has_more = records.size > POSTS_PER_PAGE
    @posts    = records.first(POSTS_PER_PAGE)
    @post     = Post.new
    # Pagination is served through lazy Turbo Frames, which request HTML.
  end

  def show
    @comment = Comment.new
  end

  def create
    @post = current_user.posts.build(post_params)

    if @post.save
      respond_to do |format|
        format.turbo_stream
        format.html { redirect_to root_path, notice: "Your post is live!" }
      end
    else
      respond_to do |format|
        format.turbo_stream do
          render turbo_stream: turbo_stream.replace("post_form", partial: "posts/composer", locals: { post: @post }),
                 status: :unprocessable_entity
        end
        format.html { redirect_to root_path, alert: @post.errors.full_messages.to_sentence }
      end
    end
  end

  def destroy
    @post = Post.find(params[:id])
    return head :forbidden unless @post.user_id == current_user.id

    @post.destroy
    respond_to do |format|
      format.turbo_stream
      format.html { redirect_to root_path, notice: "Post deleted." }
    end
  end

  private

  def set_post
    @post = Post.includes(:user, :likes, comments: :user).find(params[:id])
  end

  def post_params
    params.expect(post: %i[body image])
  end
end
