@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@push('styles')
<style>
    .tree-controls {
        display: flex;
        gap: 10px;
    }
    .tree-controls button {
        padding: 5px 10px;
        border-radius: 5px;
    }
    .tree-controls button i {
        font-size: 16px;
    }
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding: 1rem 0;
    }

    .report-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }
    #profitLossTree {
        font-family: Arial, sans-serif;
        font-size: 13px;
        background: #fff;
        border-radius: 0;
    }

    /* Tree structure */
    #profitLossTree ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #profitLossTree li {
        margin: 0;
        padding: 0;
    }

    /* Tree node styling */
    .tree-node {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.2s;
        cursor: default;
    }

    .tree-node:hover {
        background-color: #f8f9fa;
    }

    /* Toggle icon */
    .tree-toggle {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        cursor: pointer;
        color: #6c757d;
        font-size: 14px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }

    .tree-toggle:hover {
        color: #212529;
    }

    .tree-toggle.no-children {
        visibility: hidden;
    }

    .tree-toggle i {
        transition: transform 0.3s ease;
    }

    .tree-toggle.collapsed i {
        transform: rotate(-90deg);
    }

    /* Account number styling */
    .account-number {
        font-weight: 600;
        color: #495057;
        min-width: 120px;
        margin-right: 15px;
        font-size: 12px;
    }

    /* Account description styling */
    .account-description {
        flex: 1;
        color: #212529;
        font-size: 13px;
    }

    /* Account amount styling */
    .account-amount {
        font-weight: 600;
        color: #198754;
        text-align: right;
        min-width: 150px;
        font-size: 13px;
    }

    /* Level-based indentation and styling */
    .tree-children {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease-out, opacity 0.3s ease;
        opacity: 0;
    }

    .tree-children.show {
        max-height: 10000px;
        opacity: 1;
        transition: max-height 0.5s ease-in, opacity 0.3s ease;
    }

    .tree-children .tree-node {
        padding-left: 43px;
    }

    .tree-children .tree-children .tree-node {
        padding-left: 71px;
        background-color: #f8f9fa;
    }

    .tree-children .tree-children .tree-children .tree-node {
        padding-left: 99px;
        background-color: #fff;
    }

    .tree-children .tree-children .tree-children .tree-children .tree-node {
        padding-left: 127px;
    }

    /* Root level nodes - bold */
    #profitLossTree > ul > li > .tree-node {
        background-color: #e9ecef;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }

    #profitLossTree > ul > li > .tree-node .account-number,
    #profitLossTree > ul > li > .tree-node .account-description {
        font-weight: 700;
        color: #212529;
    }

    #profitLossTree > ul > li > .tree-node .account-amount {
        font-weight: 700;
        color: #0d6efd;
        font-size: 14px;
    }

    /* Table header style for tree container */
    .tree-header {
        display: flex;
        padding: 12px 15px;
        background-color: #f0f0f0;
        border: 1px solid #dee2e6;
        border-bottom: 2px solid #000;
        font-weight: 700;
        font-size: 12px;
        color: #212529;
        text-transform: uppercase;
    }

    .tree-header-number {
        min-width: 120px;
        margin-right: 15px;
    }

    .tree-header-description {
        flex: 1;
    }

    .tree-header-amount {
        min-width: 150px;
        text-align: right;
    }

    /* Container border */
    .tree-container {
        border: 1px solid #dee2e6;
        border-top: none;
        background: #fff;
    }

    .tree-wrapper {
        background: #fff;
        border-radius: 0.375rem;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="report-header">
        <h1 class="report-title">Laporan Laba Rugi</h1>
        <div class="tree-controls">
            <button id="expandAll" class="btn btn-sm btn-primary me-1">
                <i class="bi bi-chevron-down"></i> Expand All
            </button>
            <button id="collapseAll" class="btn btn-sm btn-secondary">
                <i class="bi bi-chevron-up"></i> Collapse All
            </button>
        </div>
    </div>

    <div class="tree-wrapper">
        <div class="tree-header">
            <div class="tree-header-number">Account No.</div>
            <div class="tree-header-description">Description</div>
            <div class="tree-header-amount">Amount</div>
        </div>
        <div class="tree-container">
            <div id="profitLossTree"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Data from Laravel
    const treeData = @json($tree);

    // Format number with Indonesian format
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&.').replace('.', ',').replace(/\.(\d+)$/, ',$1');
    }

    // Build tree HTML
    function buildTree(nodes, level = 0) {
        let html = '<ul>';

        nodes.forEach(node => {
            const hasChildren = node.children && node.children.length > 0;
            const toggleIcon = hasChildren ? '<i class="bi bi-chevron-down"></i>' : '';
            const toggleClass = hasChildren ? '' : 'no-children';

            html += '<li>';
            html += '<div class="tree-node">';
            html += '<span class="tree-toggle ' + toggleClass + '">' + toggleIcon + '</span>';
            html += '<span class="account-number">' + node.account_number + '</span>';
            html += '<span class="account-description">' + node.description + '</span>';
            html += '<span class="account-amount">Rp ' + formatNumber(node.amount_total) + '</span>';
            html += '</div>';

            if (hasChildren) {
                html += '<div class="tree-children show">';
                html += buildTree(node.children, level + 1);
                html += '</div>';
            }

            html += '</li>';
        });

        html += '</ul>';
        return html;
    }

    // Render tree
    $('#profitLossTree').html(buildTree(treeData));

    // Toggle node on icon click
    $(document).on('click', '.tree-toggle:not(.no-children)', function(e) {
        e.stopPropagation();
        const $toggle = $(this);
        const $node = $toggle.closest('li');
        const $children = $node.find('> .tree-children');

        $children.toggleClass('show');
        $toggle.toggleClass('collapsed');
    });

    // Expand all button
    $('#expandAll').on('click', function() {
        $('.tree-children').addClass('show');
        $('.tree-toggle').removeClass('collapsed');
    });

    // Collapse all button
    $('#collapseAll').on('click', function() {
        $('.tree-children').removeClass('show');
        $('.tree-toggle').addClass('collapsed');
    });
});
</script>
@endpush


