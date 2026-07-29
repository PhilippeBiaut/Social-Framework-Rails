class PostsController < ApplicationController
  include PaginatedPosts

  before_action :set_post, only: %i[show]

  def index
    @tab = params[:tab] == "explore" ? "explore" : "following"

    scope = @tab == "explore" ? Post.all : current_user.feed
    @posts, @has_more = page_of_posts(scope)
    @post = Post.new
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
