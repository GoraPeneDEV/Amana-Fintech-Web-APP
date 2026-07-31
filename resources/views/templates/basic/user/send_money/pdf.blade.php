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
                        <h2 class="pdf-top-title">@lang('Send Money Successful!')</h2>
                        <p class="pdf-top-text">@lang('Your send money has been successfully done.')</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="pdf-details">
            <table>
                <tbody>
                    <tr>
                        <td class="pdf-info-title">@lang('TRX')</td>
                        <td class="pdf-info-value">{{ $sendMoney->trx }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Time')</td>
                        <td class="pdf-info-value">{{ showDateTime($sendMoney->created_at) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Amount')</td>
                        <td class="pdf-info-value">{{ showAmount($sendMoney->amount) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">(+) @lang('Charge')</td>
                        <td class="pdf-info-value">{{ showAmount($sendMoney->charge) }}</td>
                    </tr>
                    <tr class="total">
                        <td class="pdf-info-title">@lang('Total')</td>
                        <td class="pdf-info-value">{{ showAmount($sendMoney->total_amount) }}</td>
                    </tr>
                    <tr class="current-balance">
                        <td class="pdf-info-title">@lang('Post Balance')</td>
                        <td class="pdf-info-value">{{ showAmount($sendMoney->sender_post_balance) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
