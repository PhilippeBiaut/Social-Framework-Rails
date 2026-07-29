require "test_helper"

# Parity with laravel/tests/Feature/SocialFlowTest.php: the Laravel port covered
# these and the Rails side did not.
class AuthorizationTest < ActionDispatch::IntegrationTest
  setup do
    @me = users(:one)
    @other = users(:two)
    sign_in_as @me
  end

  test "a user cannot delete someone else's post" do
    theirs = @other.posts.create!(body: "their post")

    assert_no_difference -> { Post.count } do
      delete post_path(theirs)
    end
    assert_response :forbidden
  end

  test "a user cannot delete someone else's comment" do
    theirs = @other.posts.create!(body: "their post")
    comment = @other.comments.create!(post: theirs, body: "their comment")

    assert_no_difference -> { Comment.count } do
      delete post_comment_path(theirs, comment)
    end
    assert Comment.exists?(comment.id), "the comment must survive"
  end

  test "a user cannot follow themselves" do
    assert_no_difference -> { Follow.count } do
      post user_follow_path(@me)
    end
    assert_empty @me.reload.following
  end

  test "a user cannot take a username that is already taken" do
    patch profile_path, params: { user: { username: @other.username } }

    assert_response :unprocessable_entity
    assert_equal "one", @me.reload.username
  end

  test "the feed only shows followed authors and myself" do
    followed = @other
    stranger = User.create!(email_address: "stranger@example.com", username: "stranger",
                            password: "password123", password_confirmation: "password123")
    @me.follow(followed)

    mine = @me.posts.create!(body: "mine only")
    theirs = followed.posts.create!(body: "followed author")
    unrelated = stranger.posts.create!(body: "not in my feed")

    get root_path
    assert_response :success
    assert_select "##{dom_id(mine)}"
    assert_select "##{dom_id(theirs)}"
    assert_select "##{dom_id(unrelated)}", false, "a stranger's post must not reach the feed"
  end

  test "people search escapes LIKE wildcards" do
    match = User.create!(email_address: "findme@example.com", username: "findme", name: "Find Me",
                         password: "password123", password_confirmation: "password123")

    get users_path(q: "%")
    assert_response :success
    assert_select "a", text: /findme/, count: 0

    get users_path(q: "findme")
    assert_response :success
    assert_select "a", text: /findme/
  end
end
