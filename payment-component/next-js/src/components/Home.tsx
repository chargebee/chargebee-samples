import Content from "@/components/Content";
import Image from 'next/image'

export default function Example() {
    return (
        <div className="min-h-full">
            <div className="py-10">
                <header>
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex flex-shrink-0 items-center">
                            <Image
                                src="/logo.svg"
                                width={500}
                                height={500}
                                alt="Picture of the author"
                                className="block h-8 w-auto"
                            />
                        </div>
                        <h1 className="text-3xl font-light leading-tight tracking-tight text-gray-900">Checkout</h1>
                    </div>
                </header>
                <main>
                    <div className="mx-auto max-w-7xl">
                        <Content/>
                    </div>
                </main>
            </div>
        </div>
    )
}
