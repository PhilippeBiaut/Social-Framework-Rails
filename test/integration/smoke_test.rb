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
end
