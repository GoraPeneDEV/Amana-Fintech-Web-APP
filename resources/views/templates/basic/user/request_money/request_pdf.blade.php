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
                        <h2 class="pdf-top-title">@lang('Received Request Money Successful!')</h2>
                        <p class="pdf-top-text">@lang('Your request money has been successfully done.')</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="pdf-details">
            <table>
                <tbody>
                    <tr>
                        <td class="pdf-info-title">@lang('Request From')</td>
                        <td class="pdf-info-value"> {{ @$requestMoney->requestSender->mobile }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Transaction ID')</td>
                        <td class="pdf-info-value">{{ $requestMoney->trx }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Time')</td>
                        <td class="pdf-info-value">{{ showDateTime($requestMoney->created_at) }}</td>
                    </tr>
                    <tr>
                        <td class="pdf-info-title">@lang('Amount')</td>
                        <td class="pdf-info-value">{{ showAmount($requestMoney->amount) }}</td>
                    </tr>

                    <tr class="total">
                        <td class="pdf-info-title">@lang('Total')</td>
                        <td class="pdf-info-value">{{ showAmount($requestMoney->amount) }}</td>
                    </tr>

                    <tr>
                        <td class="pdf-info-title">@lang('Note')</td>
                        <td class="pdf-info-value">{{ __($requestMoney->note) ?? '-' }}</td>
                    </tr>

                   
                </tbody>
            </table>
        </div>
    </div>
@endsection
