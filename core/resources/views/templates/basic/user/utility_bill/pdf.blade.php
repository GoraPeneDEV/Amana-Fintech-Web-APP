@extends($activeTemplate . 'layouts.pdf_layout')
@section('content')
    <div class="invoice-area">
        <div class="pdf-top">
            <table>
                <tr>
                    <td class="pdf-top-left">
                        <img class="pdf-top-image"
                            src="{{ 'data:image/png;base64,' . base64_encode(file_get_contents(asset($activeTemplateTrue . 'images/success.png'))) }}" />
                    </td>
                    <td class="pdf-top-right">
                        <h2 class="pdf-top-title">@lang('Bill Payment Successful!')</h2>
                        <p class="pdf-top-text">@lang('Your bill payment has been successfully done.')</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="pdf-details">
            <table>
                <tbody>
                    <tr>
                        <td class="pdf-info-title">@lang('Company')</td>
                        <td class="pdf-info-value">{{ __(@$utilityBill->company->name) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('TRX')</td>
                        <td class="pdf-info-value">{{ $utilityBill->trx }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Time')</td>
                        <td class="pdf-info-value">{{ showDateTime($utilityBill->created_at) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Amount')</td>
                        <td class="pdf-info-value">{{ showAmount($utilityBill->amount) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">(+) @lang('Charge')</td>
                        <td class="pdf-info-value">{{ showAmount($utilityBill->charge) }}</td>
                    </tr>
                    <tr class="total">
                        <td class="pdf-info-title">@lang('Total')</td>
                        <td class="pdf-info-value">{{ showAmount($utilityBill->total) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Token')</td>
                        <td class="pdf-info-value">
                            {{ $utilityBill->token ?? 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Status')</td>
                        <td class="pdf-info-value">@php echo $utilityBill->statusBadge;  @endphp</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
