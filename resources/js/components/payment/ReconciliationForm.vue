<template>
  <div class="reconciliation-form">
    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-md-4 mb-3">
          <label for="status" class="form-label">對帳狀態</label>
          <select id="status" class="form-select" v-model="form.status" required>
            <option value="pending">待對帳</option>
            <option value="matched">已對帳</option>
            <option value="unmatched">對帳不符</option>
          </select>
        </div>
        <div class="col-md-8 mb-3">
          <label for="notes" class="form-label">備註</label>
          <textarea id="notes" class="form-control" v-model="form.notes" rows="2" placeholder="請輸入對帳備註，例如：與銀行對帳單核對無誤"></textarea>
        </div>
      </div>
      
      <div class="form-text mb-3">
        <div v-if="form.status === 'matched'" class="text-success">
          <i class="bi bi-check-circle me-1"></i>選擇「已對帳」表示此日所有交易都已核對無誤
        </div>
        <div v-else-if="form.status === 'unmatched'" class="text-danger">
          <i class="bi bi-exclamation-circle me-1"></i>選擇「對帳不符」表示發現交易與金流提供商的對帳資訊不一致
        </div>
        <div v-else class="text-warning">
          <i class="bi bi-clock-history me-1"></i>選擇「待對帳」表示稍後再處理此日的對帳
        </div>
      </div>
      
      <div class="d-flex justify-content-end gap-2">
        <button 
          type="button" 
          class="btn btn-secondary" 
          data-bs-toggle="collapse" 
          data-bs-target="#reconciliationForm"
        >
          取消
        </button>
        <button 
          type="submit" 
          class="btn btn-primary"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
          儲存對帳狀態
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

export default {
  props: {
    date: {
      type: String,
      required: true
    },
    currentStatus: {
      type: String,
      default: 'pending'
    },
    currentNotes: {
      type: String,
      default: ''
    }
  },
  
  emits: ['updated'],
  
  setup(props, { emit }) {
    const loading = ref(false);
    const error = ref(null);
    
    const form = reactive({
      status: props.currentStatus,
      notes: props.currentNotes || ''
    });
    
    // 提交表單
    const submitForm = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const response = await axios.post(`/api/payments/reconciliation/${props.date}`, {
          status: form.status,
          notes: form.notes
        });
        
        // 通知父組件更新
        emit('updated', response.data.reconciliation);
        
        // 關閉表單
        const collapseEl = document.getElementById('reconciliationForm');
        const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
        if (bsCollapse) {
          bsCollapse.hide();
        }
      } catch (err) {
        error.value = err.response?.data?.message || '儲存對帳狀態失敗';
        console.error('儲存對帳狀態失敗', err);
      } finally {
        loading.value = false;
      }
    };
    
    // 當屬性變更時更新表單
    onMounted(() => {
      form.status = props.currentStatus;
      form.notes = props.currentNotes || '';
    });
    
    return {
      form,
      loading,
      error,
      submitForm
    };
  }
};
</script>

<style scoped>
.reconciliation-form {
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 8px;
}
</style>
