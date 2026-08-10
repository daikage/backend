<!DOCTYPE html>
<html>
<head>
    <title>Ride Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto;">
    <h2>Here's your receipt for your ride, {{ $ride->customer->name ?? 'Customer' }}</h2>
    
    <div style="background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3 style="margin-top: 0;">Total Fare</h3>
        <p style="font-size: 28px; font-weight: bold; margin: 0; color: #000;">
            ${{ number_format($ride->fare, 2) }}
        </p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Pickup</td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">{{ $ride->pickup_address }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Dropoff</td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">{{ $ride->dropoff_address }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; font-weight: bold;">Driver</td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: right;">{{ $ride->driver->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: bold;">Date</td>
            <td style="padding: 10px; text-align: right;">{{ $ride->created_at->format('F j, Y, g:i a') }}</td>
        </tr>
    </table>
    
    <p>Thank you for riding with Pairride!</p>
</body>
</html>
