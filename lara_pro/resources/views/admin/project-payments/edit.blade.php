@extends('admin.layouts.app')

@section('title', 'Edit '.$invoice->invoice_number)

@push('styles')
    @include('admin.project-payments.partials.styles')
@endpush

@section('content')
<div class="ppi-page">
    <section class="panel ppi-hero">
        <div><span class="eyebrow">Edit payment invoice</span><h2>{{ $invoice->invoice_number }}</h2><p>Update the percentage, commercial values, client information, or remittance details. Totals are recalculated securely when you save.</p></div>
        <div class="ppi-actions"><a class="ghost-button" href="{{ route('admin.project-payments.show', $invoice) }}">Back to preview</a></div>
    </section>

    @include('admin.project-payments.partials.form', [
        'action' => route('admin.project-payments.update', $invoice),
        'method' => 'PUT',
        'submitLabel' => 'Save invoice changes',
        'cancelUrl' => route('admin.project-payments.show', $invoice),
    ])
</div>
@endsection
