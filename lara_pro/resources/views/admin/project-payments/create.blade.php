@extends('admin.layouts.app')

@section('title', 'New Project Payment Invoice')

@push('styles')
    @include('admin.project-payments.partials.styles')
@endpush

@section('content')
<div class="ppi-page">
    <section class="panel ppi-hero">
        <div><span class="eyebrow">Progress billing</span><h2>Turn project completion into a clear payment request.</h2><p>Select a live project, confirm the completion percentage, and edit every client-facing detail before generating the PDF.</p></div>
        <div class="ppi-actions"><a class="ghost-button" href="{{ route('admin.project-payments.index') }}">Payment invoices</a></div>
    </section>

    @if ($projects->isEmpty())
        <section class="panel ppi-empty"><h3>No pipeline projects are ready yet.</h3><p>Create an active project first, then return here to generate its progress-payment invoice.</p><a class="button" href="{{ route('admin.project-management.projects.create') }}">Create project</a></section>
    @else
        @include('admin.project-payments.partials.form', [
            'action' => route('admin.project-payments.store'),
            'method' => 'POST',
            'submitLabel' => 'Generate payment invoice',
            'cancelUrl' => route('admin.project-payments.index'),
        ])
    @endif
</div>
@endsection
