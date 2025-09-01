@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-base-100 rounded-lg shadow p-6 relative">

        <a href="{{ $cancelRoute }}" class="btn btn-outline absolute top-4 right-4">Cancel</a>

        <div class="mb-6">
            @if(isset($step))
                <h1 class="text-2xl font-bold mb-2">{{ $step->title }}</h1>
                <small class="text-sm text-gray-100">{{ $stepLabel }}: {{ $step->order }}</small>
            @else
                <h1 class="text-2xl font-bold">Create {{ $type }} Email</h1>
            @endif
        </div>

        <form method="POST" action="{{ isset($step) ? $updateRoute : $storeRoute }}">
            @csrf
            @if(isset($step))
                @method('PUT')
            @endif

            @if(!isset($step))
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text mr-2">Title: </span>
                </label>
                <input type="text" name="title" class="input input-bordered"
                       value="{{ old('title', $step->title ?? '') }}" required>
                @error('title')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-control mb-6">
                <label class="label">
                    <span class="label-text">{{ $orderLabel }}</span>
                </label>
                <input type="number" name="order" class="input input-bordered"
                       value="{{ old('order', 1) }}" required {{ $orderConstraints ?? '' }}>
                @error('order')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>
            @else
            <input type="hidden" name="title" value="{{ $step->title }}">
            @endif

            <div class="form-control mb-6">
                <textarea name="content" id="content">{{ old('content', $currentContent ?? '') }}</textarea>
                @error('content')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end space-x-1">
                @if(isset($step) && isset($previewRoute))
                    <a href="{{ $cancelRoute }}" class="btn btn-outline btn-sm float-left">Cancel</a>
                    <a href="{{ $previewRoute }}" class="btn btn-info btn-sm" target="_blank">👁️ View</a>
                @endif
                @if(isset($step))
                    <button type="button" onclick="saveAndContinue()" class="btn btn-info btn-sm">Save & Continue</button>
                    <button type="submit" name="action" value="save" class="btn btn-info btn-sm">Update {{ $type }} Email</button>
                @else
                    <button type="submit" name="action" value="save" class="btn btn-info btn-sm">Create {{ $type }} Email</button>
                @endif
            </div>
        </form>
    </div>

    <div class="mt-6 p-4 bg-base-200 rounded-lg shadow">
        @if($type === 'Marketing')
        <pre>
Marketing Variables:
        VAR_firstName_VAR  - First name only
        VAR_lastName_VAR   - Last name only
        VAR_email_VAR      - Email address
        VAR_currentStep_VAR - Current step number
        VAR_unsubscribeUrl_VAR - Unsubscribe link
        </pre>
        @elseif($type === 'Newsletter')
        <pre>
Newsletter Variables:
        VAR_firstName_VAR  - First name only
        VAR_lastName_VAR   - Last name only
        VAR_email_VAR      - Email address
        VAR_currentStep_VAR - Current step number
        VAR_unsubscribeUrl_VAR - Unsubscribe link
        VAR_daysToGo_VAR   - Days until tour
        </pre>
        @elseif($type === 'Question')
        <pre>
Question Variables:
        VAR_firstName_VAR  - First name only
        VAR_lastName_VAR   - Last name only
        VAR_email_VAR      - Email address
        VAR_unsubscribeUrl_VAR - Unsubscribe link
        </pre>
        @endif
    </div>
</div>

@include('includes.tinymce-editor', ['height' => $editorHeight ?? 800])

<script>
function saveAndContinue() {
    // Sync TinyMCE content
    if (typeof tinymce !== 'undefined') {
        tinymce.triggerSave();
    }
    
    const form = document.querySelector('form');
    const formData = new FormData(form);
    formData.append('action', 'save_continue');
    
    // Get the correct form action URL
    let actionUrl = form.getAttribute('action');
    if (!actionUrl.startsWith('http')) {
        actionUrl = window.location.origin + actionUrl;
    }
    
    fetch(actionUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast('Save failed', 'error');
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        showToast('Save failed', 'error');
    });
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>
@endsection
