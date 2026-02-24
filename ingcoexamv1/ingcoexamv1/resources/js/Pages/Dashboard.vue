<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Sidebar from '../../components/Sidebar.vue'

const props = defineProps({
  errors: Object,
  entities: Object,
  posts: Array,
});

const profileDropdown = ref(false)
const newPostContent = ref('')
const postsData = ref(props.posts || [])
const editingPostId = ref(null)
const editingContent = ref('')
const editingCommentId = ref(null)
const editingCommentText = ref('')

const newComments = ref({})

// Watch for changes in props.posts and update postsData
watch(() => props.posts, (newPosts) => {
  if (newPosts) {
    postsData.value = newPosts
  }
}, { deep: true })

const toggleProfileDropdown = () => {
  profileDropdown.value = !profileDropdown.value
}

const closeProfileDropdown = () => {
  profileDropdown.value = false
}

const handleLogout = () => {
  router.post('/logout', {}, {
    onFinish: () => closeProfileDropdown()
  })
}

const handleRedirection = () => {
  router.get('/profile', {}, {
    onFinish: () => closeProfileDropdown()
  })
}

const createPost = () => {
  if (newPostContent.value.trim()) {
    router.post('/posts', {
      content: newPostContent.value,
    }, {
      onSuccess: () => {
        newPostContent.value = ''
      },
      onError: (errors) => {
        console.error('Error creating post:', errors)
      }
    })
  }
}

const startEdit = (post) => {
  editingPostId.value = post.id
  editingContent.value = post.content
}

const cancelEdit = () => {
  editingPostId.value = null``
  editingContent.value = ''
}

const saveEdit = (postId) => {
  if (editingContent.value.trim()) {
    router.put(`/posts/${postId}`, {
      content: editingContent.value,
    }, {
      onSuccess: () => {
        editingPostId.value = null
        editingContent.value = ''
      },
      onError: (errors) => {
        console.error('Error updating post:', errors)
      }
    })
  }
}

const toggleLike = (postId) => {
  router.post(`/posts/${postId}/like`, {}, {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Error toggling like:', errors)
    }
  })
}

const addComment = (postId) => {
  const commentText = newComments.value[postId]
  if (commentText && commentText.trim()) {
    router.post(`/posts/${postId}/comments`, {
      text: commentText
    }, {
      onSuccess: () => {
        newComments.value[postId] = ''
      },
      onError: (errors) => {
        console.error('Error adding comment:', errors)
      }
    })
  }
}

const deletePost = (postId) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(`/posts/${postId}`, {
      onSuccess: () => {
        console.log('Post deleted successfully')
      },
      onError: (errors) => {
        console.error('Error deleting post:', errors)
      }
    })
  }
}

const startEditComment = (comment) => {
  editingCommentId.value = comment.id
  editingCommentText.value = comment.text
}

const cancelEditComment = () => {
  editingCommentId.value = null
  editingCommentText.value = ''
}

const saveEditComment = (commentId) => {
  if (editingCommentText.value.trim()) {
    router.put(`/comments/${commentId}`, {
      text: editingCommentText.value,
    }, {
      onSuccess: () => {
        editingCommentId.value = null
        editingCommentText.value = ''
      },
      onError: (errors) => {
        console.error('Error updating comment:', errors)
      }
    })
  }
}

</script>

<template>
  <Head title="Dashboard" />
  
  <div class="dashboard-layout">
    <!-- Custom Sidebar -->
    <Sidebar :user="entities" />

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <div class="topbar">
        <div class="topbar-left">
          <h1>Welcome, {{ entities?.name || 'User' }}</h1>
        </div>

        
        <!-- Overlay to close dropdown -->
        <div 
          v-if="profileDropdown"
          class="dropdown-overlay"
          @click="closeProfileDropdown"
        ></div>
      </div>

      <!-- Page Content -->
      <div class="page-content">
        <!-- Stats Cards -->
        <!-- <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon bg-blue">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
              <h3>150</h3>
              <p>Total Orders</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon bg-green">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
              <h3>1,250</h3>
              <p>Total Customers</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon bg-purple">
              <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
              <h3>380</h3>
              <p>Total Products</p>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon bg-orange">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
              <h3>$45,320</h3>
              <p>Total Revenue</p>
            </div>
          </div>
        </div> -->

        <!-- Social Feed Section -->
        <div class="feed-section">
          <!-- Create Post Card -->
          <div class="post-card create-post">
            <div class="post-header">
              <img :src="entities?.profile_image || '/dist/img/user2-160x160.jpg'" alt="User" class="post-avatar">
              <textarea 
                v-model="newPostContent"
                placeholder="What's on your mind?"
                class="post-input"
              ></textarea>
            </div>
            <div class="post-actions">
              <!-- <button class="action-btn">
                <i class="fas fa-image"></i>
                Photo
              </button>
              <button class="action-btn">
                <i class="fas fa-smile"></i>
                Feeling
              </button> -->
              <button class="post-btn" @click="createPost">
                <i class="fas fa-paper-plane"></i>
                Post
              </button>
            </div>
          </div>

          <!-- Posts Feed -->
          <div v-for="post in postsData" :key="post.id" class="post-card">
            <!-- Post Header -->
            <div class="post-header-info">
              <img :src="post.avatar" :alt="post.author" class="post-avatar">
              <div class="post-meta">
                <p class="post-author">{{ post.author }}</p>
                <p class="post-timestamp">{{ post.timestamp }}</p>
              </div>
              <!-- Edit and Delete buttons for post owner -->
              <div v-if="post.isOwner && editingPostId !== post.id" class="post-owner-actions">
                <button 
                  class="edit-post-btn"
                  @click="startEdit(post)"
                  title="Edit post"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  class="delete-post-btn"
                  @click="deletePost(post.id)"
                  title="Delete post"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>

            <!-- Post Content (View Mode) -->
            <div v-if="editingPostId !== post.id" class="post-content">
              {{ post.content }}
            </div>

            <!-- Post Content (Edit Mode) -->
            <div v-else class="post-edit-section">
              <textarea 
                v-model="editingContent"
                class="post-edit-input"
                rows="3"
              ></textarea>
              <div class="post-edit-actions">
                <button class="btn-save" @click="saveEdit(post.id)">
                  <i class="fas fa-check"></i> Save
                </button>
                <button class="btn-cancel" @click="cancelEdit">
                  <i class="fas fa-times"></i> Cancel
                </button>
              </div>
            </div>

            <!-- Post Actions -->
            <div class="post-stats">
              <span><i class="fas fa-thumbs-up"></i> {{ post.likes }} Likes</span>
              <span><i class="fas fa-comments"></i> {{ post.comments.length }} Comments</span>
            </div>

            <div class="post-divider"></div>

            <!-- Post Buttons -->
            <div class="post-buttons">
              <button 
                class="post-action-btn"
                :class="{ liked: post.liked }"
                @click="toggleLike(post.id)"
              >
                <i class="fas fa-thumbs-up"></i>
                Like
              </button>
              <button class="post-action-btn">
                <i class="fas fa-comments"></i>
                Comment
              </button>
            </div>

            <!-- Comments Section -->
            <div class="comments-section">
              <div v-for="comment in post.comments" :key="comment.id" class="comment">
                <img :src="comment.avatar" :alt="comment.author" class="comment-avatar">
                <div class="comment-content">
                  <!-- View Mode -->
                  <template v-if="editingCommentId !== comment.id">
                    <p class="comment-author">{{ comment.author }}</p>
                    <p class="comment-text">{{ comment.text }}</p>
                  </template>
                  <!-- Edit Mode -->
                  <template v-else>
                    <textarea 
                      v-model="editingCommentText"
                      class="comment-edit-input"
                      rows="2"
                    ></textarea>
                    <div class="comment-edit-actions">
                      <button class="btn-save-sm" @click="saveEditComment(comment.id)">
                        <i class="fas fa-check"></i> Save
                      </button>
                      <button class="btn-cancel-sm" @click="cancelEditComment">
                        <i class="fas fa-times"></i> Cancel
                      </button>
                    </div>
                  </template>
                </div>
                <!-- Edit button for comment owner -->
                <button 
                  v-if="comment.isOwner && editingCommentId !== comment.id"
                  class="edit-comment-btn"
                  @click="startEditComment(comment)"
                  title="Edit comment"
                >
                  <i class="fas fa-edit"></i>
                </button>
              </div>

              <!-- Add Comment -->
              <div class="add-comment">
                <img :src="entities?.profile_image || '/dist/img/user2-160x160.jpg'" alt="User" class="comment-avatar">
                <input 
                  v-model="newComments[post.id]"
                  type="text"
                  placeholder="Write a comment..."
                  class="comment-input"
                  @keyup.enter="addComment(post.id)"
                >
                <button class="comment-btn" @click="addComment(post.id)">
                  <i class="fas fa-paper-plane"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

