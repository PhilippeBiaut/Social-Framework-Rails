require "test_helper"

class SmokeTest < ActionDispatch::IntegrationTest
  test "full happy path renders every major view" do
    # --- Sign up -------------------------------------------------------------
    post registration_path, params: { user: {
      email_address: "newbie@example.com", username: "newbie", name: "New Bie",
      bio: "Hi there", password: "password", password_confirmation: "password"
    } }
    assert_redirected_to root_path
    follow_redirect!
    assert_response :success
    assert_select "nav" # navbar shows when authenticated

    me = User.find_by!(username: "newbie")

    # --- Feed ----------------------------------------------------------------
    get root_path
    assert_response :success
    get root_path(tab: "explore")
    assert_response :success

    # --- Create a post -------------------------------------------------------
    assert_difference -> { Post.count }, 1 do
      post posts_path, params: { post: { body: "My very first post!" } }
    end
    my_post = me.posts.last

    # --- Post show + comment -------------------------------------------------
    get post_path(my_post)
    assert_response :success

    assert_difference -> { Comment.count }, 1 do
      post post_comments_path(my_post), params: { comment: { body: "Nice one" } }
    end

    # --- Like a post ---------------------------------------------------------
    other_post = Post.where.not(user: me).first
    assert_difference -> { Like.count }, 1 do
      post post_like_path(other_post)
    end
    delete post_like_path(other_post)

    # --- People + follow -----------------------------------------------------
    get users_path
    assert_response :success

    target = User.where.not(id: me.id).first
    assert_difference -> { Follow.count }, 1 do
      post user_follow_path(target)
    end
    assert me.reload.following?(target)

    # --- Profile + connections + edit ---------------------------------------
    get user_path(target)
    assert_response :success
    get followers_user_path(target)
    assert_response :success
    get following_user_path(target)
    assert_response :success
    get edit_profile_path
    assert_response :success
    patch profile_path, params: { user: { bio: "Updated bio" } }
    assert_redirected_to user_path(me)
    assert_equal "Updated bio", me.reload.bio
  end

  test "guests are redirected to sign in" do
    get root_path
    assert_redirected_to new_session_path
  end

  # Regression: the feed pages on an `id` cursor, so its ordering must be by id
  # too. Ordering by created_at while cursoring on id repeated or skipped posts
  # whenever the two disagreed (e.g. backdated records).
  test "cursor pagination walks every post exactly once" do
    author = users(:one)
    reader = users(:two)
    created = 25.times.map { |i| author.posts.create!(body: "Post number #{i}") }
    # Backdate in the opposite order of insertion to force the two orders apart.
    created.each_with_index { |post, i| post.update_column(:created_at, i.hours.ago) }

    sign_in_as reader
    expected = Post.pluck(:id).sort

    seen = []
    cursor = nil
    10.times do
      get root_path(tab: "explore", before: cursor)
      assert_response :success
      ids = css_select("#posts article").map { |el| el["id"].delete_prefix("post_").to_i }
      break if ids.empty?

      seen.concat(ids)
      cursor = ids.last
    end

    assert_equal expected.size, seen.size, "expected no duplicated or skipped posts"
    assert_equal expected, seen.sort
    assert_equal seen.sort.reverse, seen, "feed should stay newest-first across pages"
  end
end
