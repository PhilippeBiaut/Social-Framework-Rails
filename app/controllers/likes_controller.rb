class LikesController < ApplicationController
  before_action :set_post

  def create
    current_user.likes.find_or_create_by(post: @post)
    respond_with_button
  end

  def destroy
    current_user.likes.where(post: @post).destroy_all
    respond_with_button
  end

  private

  def set_post
    @post = Post.find(params[:post_id])
  end

  def respond_with_button
    @post.reload
    respond_to do |format|
      format.turbo_stream do
        render turbo_stream: turbo_stream.replace(
          "post_#{@post.id}_like",
          partial: "likes/button",
          locals: { post: @post }
        )
      end
      format.html { redirect_back fallback_location: root_path }
    end
  end
end
