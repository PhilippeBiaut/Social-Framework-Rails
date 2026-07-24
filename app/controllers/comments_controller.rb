class CommentsController < ApplicationController
  before_action :set_post, only: %i[create]

  def create
    @comment = @post.comments.build(comment_params.merge(user: current_user))

    if @comment.save
      respond_to do |format|
        format.turbo_stream
        format.html { redirect_to @post, notice: "Comment added." }
      end
    else
      respond_to do |format|
        format.turbo_stream do
          render turbo_stream: turbo_stream.replace(
            "post_#{@post.id}_comment_form",
            partial: "comments/form",
            locals: { post: @post, comment: @comment }
          ), status: :unprocessable_entity
        end
        format.html { redirect_to @post, alert: @comment.errors.full_messages.to_sentence }
      end
    end
  end

  def destroy
    @comment = current_user.comments.find(params[:id])
    @post = @comment.post
    @comment.destroy

    respond_to do |format|
      format.turbo_stream
      format.html { redirect_to @post, notice: "Comment removed." }
    end
  end

  private

  def set_post
    @post = Post.find(params[:post_id])
  end

  def comment_params
    params.expect(comment: %i[body])
  end
end
