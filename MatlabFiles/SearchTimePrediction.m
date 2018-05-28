%record_number = [38 130 628 834 1567 2593 19995 249941];
%search_time = [0.768 0.781 0.794 0.78 0.813 0.774 1.548 1.604];
record_number = [2593 199995 249941 299925];
search_time = [0.774 1.548 1.617 1.746];

predicted_record_number = 500000;
predicted_search_time = interp1(record_number, search_time, predicted_record_number, 'spline', 'extrap');
format;
disp(predicted_search_time);
plot(record_number, search_time);