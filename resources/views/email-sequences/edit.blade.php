@extends('layouts.app')

@section('content')
    <div id="email-sequence-edit-app">
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
        });

        app.use(createPinia());
        app.use(Toast);
        app.mount('#email-sequence-edit-app');
    </script>
@endpush
