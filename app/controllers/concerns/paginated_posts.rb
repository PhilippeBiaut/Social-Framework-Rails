# Cursor pagination shared by the feed and the profile.
#
# Ordering is by id, not created_at, so it matches the `before` cursor: ids are
# monotonic, which is what keeps pages from overlapping or skipping rows.
module PaginatedPosts
  extend ActiveSupport::Concern

  private

  # Returns [posts, has_more?]
  def page_of_posts(scope)
    scope = scope.with_card_data.order(id: :desc)
    scope = scope.where("posts.id < ?", params[:before]) if params[:before].present?

    records = scope.limit(Post::PER_PAGE + 1).to_a

    [ records.first(Post::PER_PAGE), records.size > Post::PER_PAGE ]
  end
end
