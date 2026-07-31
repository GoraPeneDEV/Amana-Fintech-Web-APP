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
                        <h2 class="pdf-top-title">@lang('Cash In Successful!')</h2>
                        <p class="pdf-top-text">@lang('Cash in has been successfully done.')</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="pdf-details">
            <table>
                <tbody>
                    <tr>
                        <td class="pdf-info-title">@lang('TRX')</td>
                        <td class="pdf-info-value">{{ $cashIn->trx }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Time')</td>
                        <td class="pdf-info-value">{{ showDateTime($cashIn->created_at) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Amount')</td>
                        <td class="pdf-info-value">{{ showAmount($cashIn->amount) }}</td>
                    </tr>
                  
                    <tr class="total">
                        <td class="pdf-info-title">@lang('Total')</td>
                        <td class="pdf-info-value">{{ showAmount($cashIn->amount) }}</td>
                    </tr>

                    <tr>
                        <td class="pdf-info-title">@lang('Commission') (+) </td>
                        <td class="pdf-info-value">{{ showAmount($cashIn->commission) }}</td>
                    </tr>

                    <tr class="current-balance">
                        <td class="pdf-info-title">@lang('Post Balance')</td>
                        <td class="pdf-info-value">{{ showAmount($cashIn->agent_post_balance) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
