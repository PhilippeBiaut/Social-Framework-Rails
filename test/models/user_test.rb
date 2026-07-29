require "test_helper"

class UserTest < ActiveSupport::TestCase
  test "downcases and strips email_address" do
    user = User.new(email_address: " DOWNCASED@EXAMPLE.COM ")
    assert_equal("downcased@example.com", user.email_address)
  end

  test "downcases username" do
    user = User.new(username: " AdaLovelace ")
    assert_equal("adalovelace", user.username)
  end

  test "rejects a password shorter than 8 characters" do
    user = build_user(password: "short12")

    assert_not user.valid?
    assert_includes user.errors[:password], "is too short (minimum is 8 characters)"
  end

  test "accepts a password of 8 characters" do
    assert build_user(password: "longenough").valid?
  end

  test "rejects a username with unsupported characters" do
    assert_not build_user(username: "Not Valid!").valid?
  end

  private

  def build_user(**overrides)
    User.new({
      email_address: "fresh@example.com",
      username: "freshuser",
      password: "password123",
      password_confirmation: overrides[:password] || "password123"
    }.merge(overrides))
  end
end
