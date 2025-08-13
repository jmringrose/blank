@extends('layouts.app')

@section('content')
    <div id="email-sequence-edit-app" class="bg-base-300 w-lg p-2 content-center mx-auto border border-stone-500 rounded-2xl mt-12">
        <email-sequence-edit></email-sequence-edit>
    </div>
@endsection

@push('scripts')
    <script type="module">
        import { createApp } from 'vue';
        import EmailSequenceEdit from '@/components/EmailSequenceEdit.vue';
        import { createPinia } from 'pinia';
        import Toast from "vue-toastification";
        const app = createApp({
            components: {
                EmailSequenceEdit
            }
        })
        app.use(createPinia());
        app.use(Toast);
        app.mount('#email-sequence-edit-app');
    </script>
@endpush
