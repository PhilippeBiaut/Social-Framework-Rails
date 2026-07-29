require "test_helper"

# Regression: the profile used to render every post an author had ever written,
# which took seconds once that was a few thousand rows.
class ProfilePaginationTest < ActionDispatch::IntegrationTest
  setup do
    @author = users(:two)
    sign_in_as users(:one)
  end

  test "a profile renders one page of posts, not the whole timeline" do
    (Post::PER_PAGE + 5).times { |i| @author.posts.create!(body: "Post #{i}") }

    get user_path(@author)
    assert_response :success
    assert_equal Post::PER_PAGE, css_select("article").size
  end

  test "the next page is offered as a lazy frame and walks the rest" do
    (Post::PER_PAGE + 3).times { |i| @author.posts.create!(body: "Post #{i}") }
    remaining = @author.posts.count - Post::PER_PAGE

    get user_path(@author)
    frame = css_select("turbo-frame[loading=lazy]").first
    assert frame, "expected a lazy frame to fetch the next page"

    get frame["src"]
    assert_response :success
    assert_equal remaining, css_select("article").size
  end

  test "the likes tab is paged too" do
    (Post::PER_PAGE + 2).times do |i|
      post = @author.posts.create!(body: "Post #{i}")
      @author.likes.create!(post: post)
    end

    get user_path(@author, tab: "likes")
    assert_response :success
    assert_equal Post::PER_PAGE, css_select("article").size
  end
end
