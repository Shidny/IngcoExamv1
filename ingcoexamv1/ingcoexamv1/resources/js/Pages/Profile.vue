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
const activeTab = ref('posts') // 'posts' or 'edit'
const name = ref(props.entities?.name || '')
const age = ref(props.entities?.age || '')
const address = ref(props.entities?.address || '')
const contactNumber = ref(props.entities?.contact_number || '')
const currentPassword = ref('')
const newPassword = ref('')
const newPasswordConfirmation = ref('')
const profileImage = ref(null)
const imagePreview = ref(props.entities?.profile_image || '/dist/img/user2-160x160.jpg')

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

const handleImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    profileImage.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const updateProfile = () => {
  const formData = new FormData()
  formData.append('name', name.value)
  formData.append('age', age.value || '')
  formData.append('address', address.value || '')
  formData.append('contact_number', contactNumber.value || '')
  
  if (currentPassword.value) {
    formData.append('current_password', currentPassword.value)
  }
  if (newPassword.value) {
    formData.append('password', newPassword.value)
  }
  if (newPasswordConfirmation.value) {
    formData.append('password_confirmation', newPasswordConfirmation.value)
  }
  if (profileImage.value) {
    formData.append('profile_image', profileImage.value)
  }
  formData.append('_method', 'PUT')

  router.post('/profile', formData, {
    onSuccess: () => {
      currentPassword.value = ''
      newPassword.value = ''
      newPasswordConfirmation.value = ''
      profileImage.value = null
    },
    onError: (errors) => {
      console.error('Error updating profile:', errors)
    }
  })
}

const startEdit = (post) => {
  editingPostId.value = post.id
  editingContent.value = post.content
}

const cancelEdit = () => {
  editingPostId.value = null
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
  <Head title="Profile" />
  
  <div class="dashboard-layout">
    <!-- Custom Sidebar -->
    <Sidebar :user="entities" />

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <div class="topbar">
        <div class="topbar-left">
          <h4 class="mb-0">My Profile</h4>
        </div>
      </div>

      <!-- Profile Content -->
      <div class="page-content">
        <div class="container">
          <!-- Profile Header Card -->
          <div class="profile-header-card">
            <div class="row g-3">
              <div class="col-12 col-md-auto text-center text-md-start">
                <img :src="imagePreview" alt="Profile" class="profile-header-avatar">
              </div>
              <div class="col-12 col-md">
                <div class="profile-header-info">
                  <h2 class="profile-header-name">{{ entities?.name || 'User' }}</h2>
                  <p class="profile-header-email">{{ entities?.email || 'user@example.com' }}</p>
                  <div class="row g-2 profile-stats mt-3">
                    <div class="col-4 text-center">
                      <div class="stat-item">
                        <span class="stat-number d-block">{{ postsData.length }}</span>
                        <span class="stat-label">Posts</span>
                      </div>
                    </div>
                    <div class="col-4 text-center">
                      <div class="stat-item">
                        <span class="stat-number d-block">{{ postsData.reduce((sum, post) => sum + post.likes, 0) }}</span>
                        <span class="stat-label">Likes</span>
                      </div>
                    </div>
                    <div class="col-4 text-center">
                      <div class="stat-item">
                        <span class="stat-number d-block">{{ postsData.reduce((sum, post) => sum + post.comments.length, 0) }}</span>
                        <span class="stat-label">Comments</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabs Navigation -->
          <div class="profile-tabs">
            <div class="row g-2">
              <div class="col-6">
                <button 
                  class="tab-btn w-100" 
                  :class="{ active: activeTab === 'posts' }"
                  @click="activeTab = 'posts'"
                >
                  <i class="fas fa-images"></i>
                  <span class="d-none d-sm-inline"> My Posts</span>
                </button>
              </div>
              <div class="col-6">
                <button 
                  class="tab-btn w-100" 
                  :class="{ active: activeTab === 'edit' }"
                  @click="activeTab = 'edit'"
                >
                  <i class="fas fa-user-edit"></i>
                  <span class="d-none d-sm-inline"> Edit Profile</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Scrollable Tab Content -->
          <div class="profile-content-scroll">
            <!-- Tab Content -->
            <div class="tab-content">
          <!-- My Posts Tab -->
          <div v-if="activeTab === 'posts'" class="posts-tab">
            <div v-if="postsData.length === 0" class="no-posts">
              <i class="fas fa-images"></i>
              <p>No posts yet</p>
            </div>
            
            <div v-else class="posts-grid">
              <div v-for="post in postsData" :key="post.id" class="post-card">
                <!-- Post Header -->
                <div class="post-header-info">
                  <img :src="post.avatar" :alt="post.author" class="post-avatar">
                  <div class="post-meta">
                    <p class="post-author">{{ post.author }}</p>
                    <p class="post-timestamp">{{ post.timestamp }}</p>
                  </div>
                  <!-- Edit and Delete buttons -->
                  <div v-if="editingPostId !== post.id" class="post-owner-actions">
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

          <!-- Edit Profile Tab -->
          <div v-if="activeTab === 'edit'" class="edit-tab">
            <div class="edit-profile-card">
              <form @submit.prevent="updateProfile">
                <div class="row g-3">
                  <!-- Profile Image Upload -->
                  <div class="col-12 text-center mb-3">
                    <label class="form-label fw-bold">Profile Picture</label>
                    <div class="profile-image-upload">
                      <img :src="imagePreview" alt="Profile Preview" class="profile-preview">
                      <label for="imageUpload" class="upload-btn">
                        <i class="fas fa-camera"></i> Change Photo
                      </label>
                      <input 
                        type="file" 
                        id="imageUpload" 
                        accept="image/*" 
                        @change="handleImageChange"
                        style="display: none;"
                      >
                    </div>
                    <div v-if="errors?.profile_image" class="text-danger mt-2">
                      {{ errors.profile_image }}
                    </div>
                  </div>

                  <!-- Name -->
                  <div class="col-12 col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-user"></i> Name</label>
                    <input 
                      v-model="name" 
                      type="text" 
                      class="form-control"
                      :class="errors?.name ? 'is-invalid' : ''"
                      placeholder="Your Name"
                    >
                    <div v-if="errors?.name" class="invalid-feedback">
                      {{ errors.name }}
                    </div>
                  </div>

                  <!-- Age -->
                  <div class="col-12 col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-birthday-cake"></i> Age</label>
                    <input 
                      v-model="age" 
                      type="number" 
                      class="form-control"
                      :class="errors?.age ? 'is-invalid' : ''"
                      placeholder="Your Age"
                      min="1"
                      max="150"
                    >
                    <div v-if="errors?.age" class="invalid-feedback">
                      {{ errors.age }}
                    </div>
                  </div>

                  <!-- Contact Number -->
                  <div class="col-12 col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-phone"></i> Contact Number</label>
                    <input 
                      v-model="contactNumber" 
                      type="text" 
                      class="form-control"
                      :class="errors?.contact_number ? 'is-invalid' : ''"
                      placeholder="Your Contact Number"
                    >
                    <div v-if="errors?.contact_number" class="invalid-feedback">
                      {{ errors.contact_number }}
                    </div>
                  </div>

                  <!-- Email (Read-only) -->
                  <div class="col-12 col-md-6">
                    <label class="form-label fw-bold"><i class="fas fa-envelope"></i> Email</label>
                    <input 
                      :value="entities?.email" 
                      type="email" 
                      class="form-control"
                      readonly
                      disabled
                    >
                  </div>

                  <!-- Address -->
                  <div class="col-12">
                    <label class="form-label fw-bold"><i class="fas fa-map-marker-alt"></i> Address</label>
                    <textarea 
                      v-model="address" 
                      class="form-control"
                      :class="errors?.address ? 'is-invalid' : ''"
                      placeholder="Your Address"
                      rows="3"
                    ></textarea>
                    <div v-if="errors?.address" class="invalid-feedback">
                      {{ errors.address }}
                    </div>
                  </div>

                  <!-- Divider -->
                  <div class="col-12">
                    <hr class="form-divider my-4">
                    <h4 class="form-section-title mb-3">Change Password</h4>
                  </div>

                  <!-- Current Password -->
                  <div class="col-12 col-md-4">
                    <label class="form-label fw-bold"><i class="fas fa-lock"></i> Current Password</label>
                    <input 
                      v-model="currentPassword" 
                      type="password" 
                      class="form-control"
                      :class="errors?.current_password ? 'is-invalid' : ''"
                      placeholder="Enter current password"
                    >
                    <div v-if="errors?.current_password" class="invalid-feedback">
                      {{ errors.current_password }}
                    </div>
                  </div>

                  <!-- New Password -->
                  <div class="col-12 col-md-4">
                    <label class="form-label fw-bold"><i class="fas fa-key"></i> New Password</label>
                    <input 
                      v-model="newPassword" 
                      type="password" 
                      class="form-control"
                      :class="errors?.password ? 'is-invalid' : ''"
                      placeholder="Leave blank to keep current"
                    >
                    <div v-if="errors?.password" class="invalid-feedback">
                      {{ errors.password }}
                    </div>
                  </div>

                  <!-- Confirm New Password -->
                  <div class="col-12 col-md-4">
                    <label class="form-label fw-bold"><i class="fas fa-key"></i> Confirm New Password</label>
                    <input 
                      v-model="newPasswordConfirmation" 
                      type="password" 
                      class="form-control"
                      placeholder="Confirm new password"
                    >
                  </div>

                  <!-- Submit Button -->
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-update w-100">
                      <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
