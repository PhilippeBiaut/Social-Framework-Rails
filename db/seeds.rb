# Idempotent demo data for the Social Framework.
# Run with: bin/rails db:seed   (or bin/rails db:reset to rebuild)

puts "Seeding users…"

PEOPLE = [
  { username: "ada",     name: "Ada Lovelace",     bio: "Writing the first algorithm, one loop at a time. 💻" },
  { username: "grace",   name: "Grace Hopper",     bio: "Debugging since before it was cool. 🦟" },
  { username: "linus",   name: "Linus T.",         bio: "Just for fun. Kernel enthusiast." },
  { username: "dhh",     name: "David H.",          bio: "Convention over configuration. Rails forever. 🚀" },
  { username: "yukihiro", name: "Yukihiro M.",     bio: "Optimizing for developer happiness." },
  { username: "sam",     name: "Sam Stephenson",   bio: "Hotwire, Turbo, Stimulus. HTML over the wire." }
]

users = PEOPLE.map do |attrs|
  User.find_or_create_by!(username: attrs[:username]) do |u|
    u.email_address = "#{attrs[:username]}@example.com"
    u.name = attrs[:name]
    u.bio = attrs[:bio]
    u.password = "password"
    u.password_confirmation = "password"
  end
end

ada, grace, linus, dhh, yukihiro, sam = users

puts "Building the social graph…"
# Everyone follows a couple of others.
[
  [ ada, grace ], [ ada, dhh ], [ ada, sam ],
  [ grace, ada ], [ grace, linus ],
  [ linus, yukihiro ], [ linus, dhh ],
  [ dhh, sam ], [ dhh, yukihiro ], [ dhh, ada ],
  [ yukihiro, dhh ], [ yukihiro, sam ],
  [ sam, dhh ], [ sam, ada ], [ sam, grace ]
].each { |follower, followed| follower.follow(followed) }

puts "Writing posts…"
POSTS = [
  [ ada,      "Hello world 👋 Excited to join this little corner of the internet." ],
  [ grace,    "Reminder: it's easier to ask forgiveness than permission. Ship it." ],
  [ dhh,      "Just refactored a controller down to 6 lines. Convention over configuration is undefeated." ],
  [ sam,      "Turbo Frames + Stimulus = no-build SPA feel with plain HTML. Try it, you'll love it." ],
  [ yukihiro, "A programming language should feel natural. Ruby was designed for humans first." ],
  [ linus,    "Talk is cheap. Show me the code." ],
  [ ada,      "Spent the afternoon reading about Turbo Streams. Real-time updates without a single line of custom JS 🤯" ],
  [ dhh,      "Dark mode toggle in 20 lines of Stimulus. The web platform is good, actually." ],
  [ grace,    "The most dangerous phrase is 'we've always done it this way'." ],
  [ sam,      "Small controllers, small partials, small everything. Composition scales." ]
]

posts = POSTS.map.with_index do |(user, body), i|
  post = user.posts.create!(body: body)
  post.update_column(:created_at, i.hours.ago) # spread them out on the timeline
  post
end

puts "Adding likes & comments…"
posts.each do |post|
  users.sample(rand(1..4)).each { |u| u.likes.find_or_create_by!(post: post) }
end

Comment.create!([
  { user: grace, post: posts.first, body: "Welcome aboard! 🎉" },
  { user: dhh,   post: posts.first, body: "Great to have you here." },
  { user: ada,   post: posts[3],    body: "This is exactly what I was looking for. Thanks Sam!" },
  { user: linus, post: posts[2],    body: "Six lines? Rookie numbers." }
])

puts "Done! #{User.count} users, #{Post.count} posts, #{Comment.count} comments, #{Like.count} likes."
puts "Sign in with any of: #{PEOPLE.map { |p| p[:username] + '@example.com' }.join(', ')} (password: 'password')"
