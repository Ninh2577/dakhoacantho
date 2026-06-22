<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Trường :attribute phải được chấp nhận.',
    'accepted_if' => 'Trường :attribute phải được chấp nhận khi :other là :value.',
    'active_url' => 'Trường :attribute không phải là một URL hợp lệ.',
    'after' => 'Trường :attribute phải là một ngày sau ngày :date.',
    'after_or_equal' => 'Trường :attribute phải là một ngày bằng hoặc sau ngày :date.',
    'alpha' => 'Trường :attribute chỉ có thể chứa các chữ cái.',
    'alpha_dash' => 'Trường :attribute chỉ có thể chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
    'alpha_num' => 'Trường :attribute chỉ có thể chứa chữ cái và số.',
    'array' => 'Trường :attribute phải là một danh sách (mảng).',
    'before' => 'Trường :attribute phải là một ngày trước ngày :date.',
    'before_or_equal' => 'Trường :attribute phải là một ngày bằng hoặc trước ngày :date.',
    'between' => [
        'numeric' => 'Trường :attribute phải nằm trong khoảng từ :min đến :max.',
        'file' => 'Dung lượng tệp :attribute phải từ :min đến :max kilobytes.',
        'string' => 'Trường :attribute phải từ :min đến :max ký tự.',
        'array' => 'Trường :attribute phải có từ :min đến :max phần tử.',
    ],
    'boolean' => 'Trường :attribute phải là đúng hoặc sai.',
    'confirmed' => 'Giá trị xác nhận trường :attribute không khớp.',
    'current_password' => 'Mật khẩu không chính xác.',
    'date' => 'Trường :attribute không phải là định dạng ngày tháng hợp lệ.',
    'date_equals' => 'Trường :attribute phải là một ngày bằng với :date.',
    'date_format' => 'Trường :attribute không khớp với định dạng :format.',
    'declined' => 'Trường :attribute phải bị từ chối.',
    'declined_if' => 'Trường :attribute phải bị từ chối khi :other là :value.',
    'different' => 'Trường :attribute và :other phải khác nhau.',
    'digits' => 'Trường :attribute phải gồm :digits chữ số.',
    'digits_between' => 'Trường :attribute phải có từ :min đến :max chữ số.',
    'dimensions' => 'Trường :attribute có kích thước ảnh không hợp lệ.',
    'distinct' => 'Trường :attribute có giá trị trùng lặp.',
    'doesnt_start_with' => 'Trường :attribute không được bắt đầu bằng một trong các giá trị sau: :values.',
    'email' => 'Trường :attribute phải là một địa chỉ email hợp lệ.',
    'ends_with' => 'Trường :attribute phải kết thúc bằng một trong các giá trị sau: :values.',
    'enum' => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'exists' => 'Giá trị đã chọn trong trường :attribute không tồn tại.',
    'file' => 'Trường :attribute phải là một tệp tin.',
    'filled' => 'Trường :attribute không được để trống.',
    'gt' => [
        'numeric' => 'Trường :attribute phải lớn hơn :value.',
        'file' => 'Dung lượng tệp :attribute phải lớn hơn :value kilobytes.',
        'string' => 'Trường :attribute phải dài hơn :value ký tự.',
        'array' => 'Trường :attribute phải có nhiều hơn :value phần tử.',
    ],
    'gte' => [
        'numeric' => 'Trường :attribute phải lớn hơn hoặc bằng :value.',
        'file' => 'Dung lượng tệp :attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'string' => 'Trường :attribute phải dài từ :value ký tự trở lên.',
        'array' => 'Trường :attribute phải có ít nhất :value phần tử.',
    ],
    'image' => 'Trường :attribute phải là định dạng ảnh.',
    'in' => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'in_array' => 'Trường :attribute không tồn tại trong :other.',
    'integer' => 'Trường :attribute phải là số nguyên.',
    'ip' => 'Trường :attribute phải là một địa chỉ IP hợp lệ.',
    'ipv4' => 'Trường :attribute phải là một địa chỉ IPv4 hợp lệ.',
    'ipv6' => 'Trường :attribute phải là một địa chỉ IPv6 hợp lệ.',
    'json' => 'Trường :attribute phải là một chuỗi JSON hợp lệ.',
    'lt' => [
        'numeric' => 'Trường :attribute phải nhỏ hơn :value.',
        'file' => 'Dung lượng tệp :attribute phải nhỏ hơn :value kilobytes.',
        'string' => 'Trường :attribute phải ngắn hơn :value ký tự.',
        'array' => 'Trường :attribute phải có ít hơn :value phần tử.',
    ],
    'lte' => [
        'numeric' => 'Trường :attribute phải nhỏ hơn hoặc bằng :value.',
        'file' => 'Dung lượng tệp :attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'string' => 'Trường :attribute phải dài tối đa :value ký tự.',
        'array' => 'Trường :attribute phải có tối đa :value phần tử.',
    ],
    'mac_address' => 'Trường :attribute phải là một địa chỉ MAC hợp lệ.',
    'max' => [
        'numeric' => 'Trường :attribute không được lớn hơn :max.',
        'file' => 'Dung lượng tệp :attribute không được vượt quá :max kilobytes.',
        'string' => 'Trường :attribute không được vượt quá :max ký tự.',
        'array' => 'Trường :attribute không được có nhiều hơn :max phần tử.',
    ],
    'max_digits' => 'Trường :attribute không được có nhiều hơn :max chữ số.',
    'mimes' => 'Trường :attribute phải là một tệp có định dạng: :values.',
    'mimetypes' => 'Trường :attribute phải là một tệp có định dạng: :values.',
    'min' => [
        'numeric' => 'Trường :attribute phải tối thiểu là :min.',
        'file' => 'Dung lượng tệp :attribute phải tối thiểu là :min kilobytes.',
        'string' => 'Trường :attribute phải có ít nhất :min ký tự.',
        'array' => 'Trường :attribute phải có tối thiểu :min phần tử.',
    ],
    'min_digits' => 'Trường :attribute phải có ít nhất :min chữ số.',
    'missing' => 'Trường :attribute phải bị thiếu.',
    'missing_by_mine' => 'Trường :attribute phải bị thiếu.',
    'missing_field' => 'Trường :attribute phải bị thiếu.',
    'missing_if' => 'Trường :attribute phải bị thiếu khi :other là :value.',
    'missing_unless' => 'Trường :attribute phải bị thiếu trừ khi :other là :value.',
    'multiple_of' => 'Trường :attribute phải là bội số của :value.',
    'not_in' => 'Giá trị đã chọn trong trường :attribute không hợp lệ.',
    'not_regex' => 'Định dạng trường :attribute không hợp lệ.',
    'numeric' => 'Trường :attribute phải là số.',
    'password' => [
        'letters' => 'Trường :attribute phải chứa ít nhất một chữ cái.',
        'mixed' => 'Trường :attribute phải chứa ít nhất một chữ hoa và một chữ thường.',
        'numbers' => 'Trường :attribute phải chứa ít nhất một chữ số.',
        'symbols' => 'Trường :attribute phải chứa ít nhất một ký tự đặc biệt.',
        'uncompromised' => 'Trường :attribute đã xuất hiện trong một vụ rò rỉ dữ liệu. Vui lòng chọn một :attribute khác.',
    ],
    'present' => 'Trường :attribute phải có mặt.',
    'prohibited' => 'Trường :attribute bị cấm.',
    'prohibited_if' => 'Trường :attribute bị cấm khi :other là :value.',
    'prohibited_unless' => 'Trường :attribute bị cấm trừ khi :other là một trong :values.',
    'prohibits' => 'Trường :attribute cấm :other có mặt.',
    'regex' => 'Định dạng trường :attribute không hợp lệ.',
    'required' => 'Trường :attribute là bắt buộc.',
    'required_array_keys' => 'Trường :attribute phải chứa các khóa cho: :values.',
    'required_if' => 'Trường :attribute là bắt buộc khi :other là :value.',
    'required_if_accepted' => 'Trường :attribute là bắt buộc khi :other được chấp nhận.',
    'required_unless' => 'Trường :attribute là bắt buộc trừ khi :other là một trong :values.',
    'required_with' => 'Trường :attribute là bắt buộc khi có mặt :values.',
    'required_with_all' => 'Trường :attribute là bắt buộc khi có mặt tất cả :values.',
    'required_without' => 'Trường :attribute là bắt buộc khi không có mặt :values.',
    'required_without_all' => 'Trường :attribute là bắt buộc khi tất cả :values không có mặt.',
    'same' => 'Trường :attribute và :other phải trùng nhau.',
    'size' => [
        'numeric' => 'Trường :attribute phải bằng :size.',
        'file' => 'Dung lượng tệp :attribute phải bằng :size kilobytes.',
        'string' => 'Trường :attribute phải dài đúng :size ký tự.',
        'array' => 'Trường :attribute phải chứa đúng :size phần tử.',
    ],
    'starts_with' => 'Trường :attribute phải bắt đầu bằng một trong các giá trị sau: :values.',
    'string' => 'Trường :attribute phải là một chuỗi ký tự.',
    'timezone' => 'Trường :attribute phải là một múi giờ hợp lệ.',
    'unique' => 'Trường :attribute đã tồn tại trong hệ thống.',
    'uploaded' => 'Tải tệp tin trường :attribute lên thất bại.',
    'uppercase' => 'Trường :attribute phải là chữ in hoa.',
    'url' => 'Trường :attribute phải là một đường dẫn URL hợp lệ.',
    'uuid' => 'Trường :attribute phải là định dạng UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'title' => 'tiêu đề',
        'title_vietnamese' => 'tiêu đề',
        'title_english' => 'tiêu đề tiếng Anh',
        'slug' => 'đường dẫn (slug)',
        'content' => 'nội dung',
        'excerpt' => 'tóm tắt',
        'category_id' => 'danh mục',
        'author_id' => 'tác giả',
        'featured_image' => 'ảnh đại diện',
        'name' => 'họ tên',
        'email' => 'email',
        'password' => 'mật khẩu',
        'phone' => 'số điện thoại',
        'role' => 'vai trò',
        'status' => 'trạng thái',
        'symptoms' => 'triệu chứng',
        'notes' => 'ghi chú',
        'assigned_to' => 'người phụ trách',
        'department' => 'chuyên khoa',
        'gender' => 'giới tính',
        'birth_date' => 'ngày sinh',
        'age' => 'tuổi',
        'address' => 'địa chỉ',
        'source' => 'nguồn khách',
        'internal_note' => 'ghi chú nội bộ',
        'xml_file' => 'tệp XML WordPress',
        'old_domain' => 'tên miền cũ',
        'media_mode' => 'chế độ lưu trữ ảnh',
        'local_media_base_path' => 'đường dẫn cục bộ ảnh cũ',
        'import_post_types' => 'loại bài viết',
        'import_statuses' => 'trạng thái bài viết',
        'duplicate_mode' => 'chế độ trùng lặp',
        'dry_run' => 'chạy thử nghiệm',
        'limit' => 'giới hạn',
        'article_pattern' => 'định dạng URL bài viết',
        'category_pattern' => 'định dạng URL danh mục',
        'layout' => 'khối giao diện',
    ],

];
